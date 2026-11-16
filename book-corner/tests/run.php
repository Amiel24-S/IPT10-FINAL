<?php
/**
 * A tiny test runner (no PHPUnit needed).
 * Run from the project folder:   php tests/run.php
 */

require __DIR__ . '/../spine/autoload.php';
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/spine/helpers.php';

use App\Models\Book;
use App\Models\Order;
use App\Services\Cart;
use App\Services\OpenLibrary;
use Spine\Database;
use Spine\Request;
use Spine\Response;
use Spine\Router;

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "  \033[32m✔\033[0m $name\n";
        $passed++;
    } catch (Throwable $e) {
        echo "  \033[31m✘ $name\033[0m\n    " . $e->getMessage() . "\n";
        $failed++;
    }
}

function check(bool $condition, string $message = 'Expected true'): void
{
    if (!$condition) {
        throw new Exception($message);
    }
}

/** Fresh in-memory database with two books before each database test. */
function freshDatabase(): void
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec(file_get_contents(__DIR__ . '/sqlite-schema.sql'));
    Database::setConnection($pdo);
    Book::create(['isbn' => '1111111111', 'title' => 'Book A', 'author' => 'Ann', 'price' => 100, 'stock' => 5]);
    Book::create(['isbn' => '2222222222', 'title' => 'Book B', 'author' => 'Ben', 'price' => 250, 'stock' => 1]);
    $_SESSION = [];
}

/** Pretend the browser asked for METHOD /path */
function fakeRequest(string $method, string $path): Request
{
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $path;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    return new Request();
}

class FakeController
{
    public function show(Request $r) { return 'book ' . $r->param('id'); }
}

class BlockEverything implements Spine\Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        return Response::html('blocked', 403);
    }
}

echo "\nRouter\n";

test('matches a route and reads the {id} parameter', function () {
    $router = new Router();
    $router->get('/books/{id}', [FakeController::class, 'show']);
    $response = $router->dispatch(fakeRequest('GET', '/books/42'));
    check($response->body === 'book 42', "Got: {$response->body}");
});

test('ignores a route with the wrong HTTP method', function () {
    $router = new Router();
    $router->post('/books/{id}', [FakeController::class, 'show']);
    $response = $router->dispatch(fakeRequest('GET', '/books/42'));
    check($response->status === 404, "Expected 404, got {$response->status}");
});

test('middleware can stop a request', function () {
    $router = new Router();
    $router->alias('block', BlockEverything::class);
    $router->get('/secret', [FakeController::class, 'show'])->middleware('block');
    $response = $router->dispatch(fakeRequest('GET', '/secret'));
    check($response->status === 403 && $response->body === 'blocked');
});

test('Request strips the project folder from the path', function () {
    $_SERVER['REQUEST_URI'] = '/book-corner/public/books/3?x=1';
    $_SERVER['SCRIPT_NAME'] = '/book-corner/public/index.php';
    check((new Request())->path() === '/books/3');
});

echo "\nCart\n";

test('adds books and calculates the total', function () {
    freshDatabase();
    Cart::add(1, 2);   // 2 x 100
    Cart::add(2);      // 1 x 250
    check(Cart::count() === 3, 'count should be 3');
    check(Cart::total() == 450, 'total should be 450, got ' . Cart::total());
});

test('setting quantity to 0 removes the book', function () {
    freshDatabase();
    Cart::add(1);
    Cart::setQuantity(1, 0);
    check(Cart::count() === 0);
});

echo "\nOrders\n";

test('placing an order lowers the stock', function () {
    freshDatabase();
    Cart::add(1, 2);
    Order::place(1, '123 Test Street, Manila', Cart::lines());
    check(Book::find(1)['stock'] == 3, 'stock should drop from 5 to 3');
});

test('an order with too little stock is cancelled completely', function () {
    freshDatabase();
    Cart::add(1, 1);
    Cart::add(2, 5);   // only 1 in stock
    try {
        Order::place(1, '123 Test Street, Manila', Cart::lines());
        throw new Exception('Should have thrown');
    } catch (RuntimeException $e) {
    }
    check(Book::find(1)['stock'] == 5, 'Book A stock must be unchanged (rollback)');
    check(count(Order::all()) === 0, 'No order should be saved');
});

echo "\nOpen Library API\n";

test('rejects an ISBN with the wrong length (no API call made)', function () {
    $api = new OpenLibrary();
    check($api->findByIsbn('12345') === null);
    check(str_contains($api->error, '10 or 13'));
});

test('reads the fields out of an Open Library JSON response', function () {
    $json = '{"ISBN:9780140328721": {"title": "Fantastic Mr Fox",
              "authors": [{"name": "Roald Dahl"}], "publishers": [{"name": "Puffin"}],
              "publish_date": "October 1, 1988", "number_of_pages": 96,
              "cover": {"medium": "https://covers.openlibrary.org/b/id/8739161-M.jpg"}}}';
    $book = (new OpenLibrary())->parse($json, '9780140328721');
    check($book['title'] === 'Fantastic Mr Fox');
    check($book['author'] === 'Roald Dahl');
    check($book['year'] === 1988 && $book['pages'] === 96);
});

test('reports "not found" when the API returns an empty object', function () {
    $api = new OpenLibrary();
    check($api->parse('{}', '9780000000000') === null);
    check(str_contains($api->error, 'No book found'));
});

test('reads a cached API response', function () {
    $file = BASE_PATH . '/storage/cache/isbn_9999999999.json';
    file_put_contents($file, json_encode(['isbn' => '9999999999', 'title' => 'Cached Book']));
    $book = (new OpenLibrary())->findByIsbn('999-999-9999');
    unlink($file);
    check($book['title'] === 'Cached Book');
});

echo "\n" . ($failed ? "\033[31m" : "\033[32m") . "$passed passed, $failed failed\033[0m\n\n";
exit($failed ? 1 : 0);
