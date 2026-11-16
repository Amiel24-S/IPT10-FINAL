<?php
namespace Spine;

/**
 * Request - wraps everything about the incoming HTTP request
 * (method, path, form data, query string) in one object.
 */
class Request
{
    public array $params = [];   // filled by the Router, e.g. {id} in /books/{id}

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * The path the user asked for, without the project folder.
     * http://localhost/book-corner/public/books/3  ->  /books/3
     */
    public function path(): string
    {
        $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '/index.php' ? '/' : $uri;
    }

    /** Read a value from POST first, then GET. */
    public function input(string $key, $default = null)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public function all(): array
    {
        return array_map(fn($v) => is_string($v) ? trim($v) : $v, array_merge($_GET, $_POST));
    }

    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }
}
