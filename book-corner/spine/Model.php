<?php
namespace Spine;

/**
 * Model - base class for database tables.
 * A child class only has to set $table and $fillable.
 */
abstract class Model
{
    protected static string $table = '';
    protected static array $fillable = [];

    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::query("SELECT * FROM " . static::$table . " ORDER BY $orderBy")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::query("SELECT * FROM " . static::$table . " WHERE id = ?", [$id])->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $data = array_intersect_key($data, array_flip(static::$fillable));
        $columns = implode(', ', array_keys($data));
        $marks   = implode(', ', array_fill(0, count($data), '?'));
        Database::query("INSERT INTO " . static::$table . " ($columns) VALUES ($marks)", array_values($data));
        return Database::lastId();
    }

    public static function update(int $id, array $data): void
    {
        $data = array_intersect_key($data, array_flip(static::$fillable));
        $set  = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        Database::query("UPDATE " . static::$table . " SET $set WHERE id = ?", [...array_values($data), $id]);
    }

    public static function delete(int $id): void
    {
        Database::query("DELETE FROM " . static::$table . " WHERE id = ?", [$id]);
    }
}
