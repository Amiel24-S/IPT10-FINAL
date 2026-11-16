<?php
/**
 * All the URLs of Book Corner, in one place.
 * Format:  $router->METHOD('/path', [Controller::class, 'method'])->middleware(...)
 */

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\LogMiddleware;

/** @var \Spine\Router $router */

// Middleware that runs on every request
$router->useGlobal(LogMiddleware::class);
$router->useGlobal(CsrfMiddleware::class);

// Short names for route middleware
$router->alias('auth', AuthMiddleware::class);
$router->alias('admin', AdminMiddleware::class);

// Public pages
$router->get('/', [HomeController::class, 'index']);
$router->get('/books', [BookController::class, 'index']);
$router->get('/books/{id}', [BookController::class, 'show']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

// Checkout and orders (must be logged in)
$router->get('/checkout', [CartController::class, 'checkout'])->middleware('auth');
$router->post('/checkout', [CartController::class, 'placeOrder'])->middleware('auth');
$router->get('/orders', [CartController::class, 'orders'])->middleware('auth');

// Accounts
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// Admin (must be an admin)
$router->get('/admin', [AdminController::class, 'index'])->middleware('admin');
$router->get('/admin/orders', [AdminController::class, 'orders'])->middleware('admin');
$router->get('/admin/books/new', [AdminController::class, 'create'])->middleware('admin');
$router->post('/admin/books', [AdminController::class, 'store'])->middleware('admin');
$router->get('/admin/books/{id}/edit', [AdminController::class, 'edit'])->middleware('admin');
$router->post('/admin/books/{id}', [AdminController::class, 'update'])->middleware('admin');
$router->post('/admin/books/{id}/delete', [AdminController::class, 'delete'])->middleware('admin');

// JSON endpoint that shows the Open Library API result
$router->get('/api/isbn/{isbn}', [AdminController::class, 'isbnJson'])->middleware('admin');
