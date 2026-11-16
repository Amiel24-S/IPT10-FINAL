<?php
namespace App\Models;

use Spine\Database;
use Spine\Model;

class Order extends Model
{
    protected static string $table = 'orders';
    protected static array $fillable = ['user_id', 'total', 'status', 'address'];

    /**
     * Save an order and its items in one transaction.
     * If anything fails (e.g. not enough stock), nothing is saved.
     */
    public static function place(int $userId, string $address, array $lines): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $total = array_sum(array_column($lines, 'subtotal'));
            $orderId = self::create([
                'user_id' => $userId, 'total' => $total, 'status' => 'pending', 'address' => $address,
            ]);

            foreach ($lines as $line) {
                $book = $line['book'];
                $updated = Database::query(
                    "UPDATE books SET stock = stock - ? WHERE id = ? AND stock >= ?",
                    [$line['qty'], $book['id'], $line['qty']]
                )->rowCount();
                if ($updated === 0) {
                    throw new \RuntimeException("Sorry, not enough stock for \"{$book['title']}\".");
                }
                Database::query(
                    "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)",
                    [$orderId, $book['id'], $line['qty'], $book['price']]
                );
            }

            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function forUser(int $userId): array
    {
        return Database::query("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC", [$userId])->fetchAll();
    }

    public static function allWithCustomer(): array
    {
        return Database::query("SELECT o.*, u.name AS customer FROM orders o
                JOIN users u ON u.id = o.user_id ORDER BY o.id DESC")->fetchAll();
    }

    public static function items(int $orderId): array
    {
        return Database::query("SELECT oi.*, b.title FROM order_items oi
                JOIN books b ON b.id = oi.book_id WHERE oi.order_id = ?", [$orderId])->fetchAll();
    }
}
