<?php
namespace App\Services;

use App\Models\Book;
use Spine\Session;

/**
 * Cart - the shopping cart is kept in the session as [bookId => quantity].
 */
class Cart
{
    public static function items(): array
    {
        return Session::get('cart', []);
    }

    public static function add(int $bookId, int $qty = 1): void
    {
        $cart = self::items();
        $cart[$bookId] = ($cart[$bookId] ?? 0) + max(1, $qty);
        Session::set('cart', $cart);
    }

    public static function setQuantity(int $bookId, int $qty): void
    {
        $cart = self::items();
        if ($qty <= 0) {
            unset($cart[$bookId]);
        } else {
            $cart[$bookId] = $qty;
        }
        Session::set('cart', $cart);
    }

    public static function remove(int $bookId): void
    {
        self::setQuantity($bookId, 0);
    }

    public static function clear(): void
    {
        Session::forget('cart');
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    /** Cart rows with the book details and line totals filled in. */
    public static function lines(): array
    {
        $lines = [];
        foreach (self::items() as $id => $qty) {
            $book = Book::find((int) $id);
            if ($book) {
                $lines[] = ['book' => $book, 'qty' => $qty, 'subtotal' => $book['price'] * $qty];
            }
        }
        return $lines;
    }

    public static function total(): float
    {
        return array_sum(array_column(self::lines(), 'subtotal'));
    }
}
