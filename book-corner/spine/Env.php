<?php
namespace Spine;

/**
 * Env - reads the .env file into memory.
 * Each line looks like KEY=value. Lines starting with # are comments.
 */
class Env
{
    private static array $values = [];

    public static function load(string $file): void
    {
        if (!file_exists($file)) {
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            self::$values[trim($key)] = trim($value, " \"'");
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$values[$key] ?? $default;
    }
}
