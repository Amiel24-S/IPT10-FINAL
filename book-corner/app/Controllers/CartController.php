<?php
namespace App\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Services\Cart;
use Spine\Controller;
use Spine\Request;
use Spine\Response;

class CartController extends Controller
{
    /** GET /cart */
    public function index(Request $request): Response
    {
        return $this->view('cart/index', [
            'title' => 'Your cart',
            'lines' => Cart::lines(),
            'total' => Cart::total(),
        ]);
    }

    /** POST /cart/add */
    public function add(Request $request): Response
    {
        $book = Book::find((int) $request->input('book_id'));
        if (!$book || $book['stock'] < 1) {
            return $this->redirect('/books', 'Sorry, that book is out of stock.', 'error');
        }
        Cart::add($book['id'], (int) $request->input('qty', 1));
        return $this->redirect('/cart', "\"{$book['title']}\" was added to your cart.");
    }

    /** POST /cart/update */
    public function update(Request $request): Response
    {
        Cart::setQuantity((int) $request->input('book_id'), (int) $request->input('qty'));
        return $this->redirect('/cart', 'Cart updated.');
    }

    /** POST /cart/remove */
    public function remove(Request $request): Response
    {
        Cart::remove((int) $request->input('book_id'));
        return $this->redirect('/cart', 'Book removed from cart.');
    }

    /** GET /checkout  (needs login) */
    public function checkout(Request $request): Response
    {
        if (Cart::count() === 0) {
            return $this->redirect('/books', 'Your cart is empty.', 'error');
        }
        return $this->view('cart/checkout', [
            'title' => 'Checkout',
            'lines' => Cart::lines(),
            'total' => Cart::total(),
        ]);
    }

    /** POST /checkout  (needs login) */
    public function placeOrder(Request $request): Response
    {
        $address = (string) $request->input('address');
        if (strlen($address) < 10) {
            return $this->redirect('/checkout', 'Please enter your full delivery address.', 'error');
        }
        try {
            $orderId = Order::place($this->user()['id'], $address, Cart::lines());
        } catch (\RuntimeException $e) {
            return $this->redirect('/cart', $e->getMessage(), 'error');
        }
        Cart::clear();
        return $this->redirect('/orders', "Thank you! Order #$orderId has been placed.");
    }

    /** GET /orders  (needs login) */
    public function orders(Request $request): Response
    {
        $orders = Order::forUser($this->user()['id']);
        foreach ($orders as &$order) {
            $order['items'] = Order::items($order['id']);
        }
        return $this->view('orders/index', ['title' => 'My orders', 'orders' => $orders]);
    }
}
