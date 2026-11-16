<?php
namespace Spine;

/**
 * Response - what we send back to the browser:
 * a status code, headers and a body.
 */
class Response
{
    public function __construct(
        public string $body = '',
        public int $status = 200,
        public array $headers = ['Content-Type' => 'text/html; charset=UTF-8']
    ) {}

    public static function html(string $html, int $status = 200): self
    {
        return new self($html, $status);
    }

    public static function json($data, int $status = 200): self
    {
        return new self(json_encode($data), $status, ['Content-Type' => 'application/json']);
    }

    public static function redirect(string $path): self
    {
        return new self('', 302, ['Location' => url($path)]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->body;
    }
}
