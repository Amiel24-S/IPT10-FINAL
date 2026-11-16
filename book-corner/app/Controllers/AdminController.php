<?php
namespace App\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Services\OpenLibrary;
use Spine\Controller;
use Spine\Database;
use Spine\Request;
use Spine\Response;

class AdminController extends Controller
{
    /** GET /admin - dashboard with all books */
    public function index(Request $request): Response
    {
        return $this->view('admin/index', [
            'title' => 'Admin dashboard',
            'books' => Book::search(),
            'stats' => [
                'books'   => Database::query("SELECT COUNT(*) FROM books")->fetchColumn(),
                'orders'  => Database::query("SELECT COUNT(*) FROM orders")->fetchColumn(),
                'revenue' => Database::query("SELECT COALESCE(SUM(total),0) FROM orders")->fetchColumn(),
            ],
        ]);
    }

    /** GET /admin/books/new  (?isbn=... fills the form from Open Library) */
    public function create(Request $request): Response
    {
        $book = ['isbn' => '', 'title' => '', 'author' => '', 'publisher' => '', 'year' => '',
                 'pages' => '', 'price' => '', 'stock' => 10, 'cover_url' => '', 'description' => '', 'category_id' => ''];
        $apiMessage = null;

        $isbn = (string) $request->input('isbn', '');
        if ($isbn !== '') {
            $api = new OpenLibrary();
            $found = $api->findByIsbn($isbn);
            if ($found) {
                $book = array_merge($book, array_filter($found, fn($v) => $v !== null));
                $apiMessage = ['type' => 'success', 'text' => 'Details filled in from Open Library. Add a price and save.'];
            } else {
                $book['isbn'] = $isbn;
                $apiMessage = ['type' => 'error', 'text' => $api->error];
            }
        }

        return $this->view('admin/form', [
            'title'      => 'Add a book',
            'book'       => $book,
            'action'     => '/admin/books',
            'categories' => Category::all('name'),
            'apiMessage' => $apiMessage,
        ]);
    }

    /** POST /admin/books */
    public function store(Request $request): Response
    {
        $data = $this->validated($request);
        if (is_string($data)) {
            return $this->redirect('/admin/books/new', $data, 'error');
        }
        Book::create($data);
        return $this->redirect('/admin', "\"{$data['title']}\" was added.");
    }

    /** GET /admin/books/{id}/edit */
    public function edit(Request $request): Response
    {
        $book = Book::find((int) $request->param('id'));
        if (!$book) {
            return $this->redirect('/admin', 'Book not found.', 'error');
        }
        return $this->view('admin/form', [
            'title'      => 'Edit book',
            'book'       => $book,
            'action'     => "/admin/books/{$book['id']}",
            'categories' => Category::all('name'),
            'apiMessage' => null,
        ]);
    }

    /** POST /admin/books/{id} */
    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $data = $this->validated($request);
        if (is_string($data)) {
            return $this->redirect("/admin/books/$id/edit", $data, 'error');
        }
        Book::update($id, $data);
        return $this->redirect('/admin', "\"{$data['title']}\" was updated.");
    }

    /** POST /admin/books/{id}/delete */
    public function delete(Request $request): Response
    {
        try {
            Book::delete((int) $request->param('id'));
        } catch (\PDOException $e) {
            return $this->redirect('/admin', 'This book is part of an order, so it cannot be deleted. Set its stock to 0 instead.', 'error');
        }
        return $this->redirect('/admin', 'Book deleted.');
    }

    /** GET /admin/orders */
    public function orders(Request $request): Response
    {
        return $this->view('admin/orders', ['title' => 'All orders', 'orders' => Order::allWithCustomer()]);
    }

    /** GET /api/isbn/{isbn} - shows the raw API result as JSON (handy for the demo) */
    public function isbnJson(Request $request): Response
    {
        $api = new OpenLibrary();
        $book = $api->findByIsbn((string) $request->param('isbn'));
        return $book
            ? Response::json(['ok' => true, 'book' => $book])
            : Response::json(['ok' => false, 'error' => $api->error], 404);
    }

    /** Check the book form. Returns clean data, or an error message string. */
    private function validated(Request $request)
    {
        $data = [
            'category_id' => (int) $request->input('category_id') ?: null,
            'isbn'        => preg_replace('/[^0-9X]/i', '', (string) $request->input('isbn')),
            'title'       => (string) $request->input('title'),
            'author'      => (string) $request->input('author'),
            'publisher'   => (string) $request->input('publisher'),
            'year'        => (int) $request->input('year') ?: null,
            'pages'       => (int) $request->input('pages') ?: null,
            'price'       => (float) $request->input('price'),
            'stock'       => max(0, (int) $request->input('stock')),
            'cover_url'   => (string) $request->input('cover_url'),
            'description' => (string) $request->input('description'),
        ];
        if ($data['title'] === '' || $data['author'] === '') {
            return 'Title and author are required.';
        }
        if ($data['price'] <= 0) {
            return 'Please enter a price greater than 0.';
        }
        return $data;
    }
}
