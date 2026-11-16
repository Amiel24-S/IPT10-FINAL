<?php
namespace Spine;

/**
 * App - starts everything: loads .env, starts the session,
 * builds the router, and sends the response.
 */
class App
{
    public Router $router;

    public function __construct(string $basePath)
    {
        define('BASE_PATH', $basePath);
        Env::load($basePath . '/.env');
        require_once __DIR__ . '/helpers.php';

        date_default_timezone_set(Env::get('APP_TIMEZONE', 'Asia/Manila'));
        Session::start();

        $this->router = new Router();
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch(new Request());
        } catch (\Throwable $e) {
            error_log('[Spine] ' . $e->getMessage());
            $message = Env::get('APP_DEBUG') === 'true'
                ? $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
                : 'Something went wrong. Please try again.';
            $response = Response::html(View::render('errors/500', ['title' => 'Error', 'message' => $message]), 500);
        }
        $response->send();
    }
}
