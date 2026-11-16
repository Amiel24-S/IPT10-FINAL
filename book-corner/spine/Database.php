<?php
namespace Spine;

use PDO;

/**
 * Database - one shared PDO connection, created the first time it is needed.
 * Settings come from the .env file.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $driver = Env::get('DB_DRIVER', 'mysql');

            if ($driver === 'sqlite') {
                $dsn = 'sqlite:' . Env::get('DB_DATABASE');
                self::$pdo = new PDO($dsn);
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    Env::get('DB_HOST', '127.0.0.1'),
                    Env::get('DB_PORT', '3306'),
                    Env::get('DB_DATABASE', 'book_corner')
                );
                self::$pdo = new PDO($dsn, Env::get('DB_USERNAME', 'root'), Env::get('DB_PASSWORD', ''));
            }

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return self::$pdo;
    }

    /** Run a prepared statement. Values are bound safely (no SQL injection). */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function lastId(): int
    {
        return (int) self::connection()->lastInsertId();
    }

    /** Used by tests to swap in a different database. */
    public static function setConnection(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
