<?php
/**
 * Small global helper functions used in views and controllers.
 */

use Spine\Env;
use Spine\Session;

/** Build a full link: url('/books') -> http://localhost/book-corner/public/books */
function url(string $path = '/'): string
{
    return rtrim(Env::get('APP_URL', ''), '/') . '/' . ltrim($path, '/');
}

/** Escape text before printing it in HTML (stops XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Format a price in pesos. */
function peso($amount): string
{
    return '₱' . number_format((float) $amount, 2);
}

/** Hidden CSRF field to put inside every POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . Session::token() . '">';
}

/** Cover image for a book: saved URL, or Open Library's cover by ISBN. */
function cover(array $book, string $size = 'M'): string
{
    if (!empty($book['cover_url'])) {
        return $book['cover_url'];
    }
    return 'https://covers.openlibrary.org/b/isbn/' . urlencode($book['isbn']) . "-$size.jpg?default=false";
}
