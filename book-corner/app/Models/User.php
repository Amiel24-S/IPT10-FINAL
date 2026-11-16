<?php
namespace App\Models;

use Spine\Database;
use Spine\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email', 'password', 'role'];

    public static function findByEmail(string $email): ?array
    {
        $row = Database::query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        return $row ?: null;
    }
}
