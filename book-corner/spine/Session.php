<?php
namespace Spine;

/**
 * Session - small helper around $_SESSION, flash messages and CSRF tokens.
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** A message shown once on the next page, e.g. "Book added to cart". */
    public static function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['_flash'] = ['message' => $message, 'type' => $type];
    }

    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return $flash;
    }

    /** CSRF token: a random secret added to every form so other sites can't submit them. */
    public static function token(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_token'];
    }
}
