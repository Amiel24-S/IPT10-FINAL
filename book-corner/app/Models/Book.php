<?php
namespace App\Models;

use Spine\Database;
use Spine\Model;

class Book extends Model
{
    protected static string $table = 'books';
    protected static array $fillable = [
        'category_id', 'isbn', 'title', 'author', 'publisher',
        'year', 'pages', 'price', 'stock', 'cover_url', 'description',
    ];

    /** Books with their category name, optionally filtered by search text or category. */
    public static function search(string $q = '', ?int $categoryId = null): array
    {
        $sql = "SELECT b.*, c.name AS category FROM books b
                LEFT JOIN categories c ON c.id = b.category_id WHERE 1=1";
        $params = [];
        if ($q !== '') {
            $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
            array_push($params, "%$q%", "%$q%", "%$q%");
        }
        if ($categoryId) {
            $sql .= " AND b.category_id = ?";
            $params[] = $categoryId;
        }
        return Database::query($sql . " ORDER BY b.title", $params)->fetchAll();
    }

    public static function latest(int $limit = 4): array
    {
        return Database::query("SELECT * FROM books ORDER BY id DESC LIMIT " . (int) $limit)->fetchAll();
    }

    public static function withCategory(int $id): ?array
    {
        $row = Database::query("SELECT b.*, c.name AS category FROM books b
                LEFT JOIN categories c ON c.id = b.category_id WHERE b.id = ?", [$id])->fetch();
        return $row ?: null;
    }
}
