<?php
namespace App\Services;

/**
 * OpenLibrary - talks to the free Open Library API (openlibrary.org).
 * No API key needed. Given an ISBN, it returns the title, author,
 * publisher, year, page count and cover image.
 *
 * Results are cached in storage/cache for 1 day so we don't ask
 * the API for the same book again and again (rate limiting).
 */
class OpenLibrary
{
    private const ENDPOINT = 'https://openlibrary.org/api/books';
    private const CACHE_SECONDS = 86400;

    public ?string $error = null;

    public function findByIsbn(string $isbn): ?array
    {
        $isbn = preg_replace('/[^0-9X]/i', '', $isbn);   // remove dashes and spaces
        if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
            $this->error = 'An ISBN must have 10 or 13 digits.';
            return null;
        }

        // 1. Use the cached answer if we have a fresh one
        $cacheFile = BASE_PATH . "/storage/cache/isbn_$isbn.json";
        if (file_exists($cacheFile) && time() - filemtime($cacheFile) < self::CACHE_SECONDS) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        // 2. Call the API:  /api/books?bibkeys=ISBN:...&format=json&jscmd=data
        $url = self::ENDPOINT . '?' . http_build_query([
            'bibkeys' => "ISBN:$isbn",
            'format'  => 'json',
            'jscmd'   => 'data',
        ]);
        $json = $this->get($url);
        if ($json === null) {
            return null;   // $this->error was set by get()
        }

        // 3. Read the JSON response
        $result = $this->parse($json, $isbn);
        if ($result) {
            @file_put_contents($cacheFile, json_encode($result));
        }
        return $result;
    }

    /** Turn Open Library's JSON into the fields our books table uses. */
    public function parse(string $json, string $isbn): ?array
    {
        $data = json_decode($json, true);
        $book = $data["ISBN:$isbn"] ?? null;
        if (!$book) {
            $this->error = "No book found on Open Library for ISBN $isbn.";
            return null;
        }

        return [
            'isbn'      => $isbn,
            'title'     => $book['title'] ?? '',
            'author'    => implode(', ', array_column($book['authors'] ?? [], 'name')),
            'publisher' => $book['publishers'][0]['name'] ?? '',
            'year'      => $this->year($book['publish_date'] ?? ''),
            'pages'     => $book['number_of_pages'] ?? null,
            'cover_url' => $book['cover']['medium'] ?? '',
        ];
    }

    /** Send a GET request with cURL. Returns the body or null on failure. */
    private function get(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_USERAGENT      => 'BookCorner-School-Project/1.0',
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            $this->error = "Could not reach Open Library ($curlError). Check your internet connection.";
            return null;
        }
        if ($status === 429) {
            $this->error = 'Open Library is busy (too many requests). Wait a minute and try again.';
            return null;
        }
        if ($status !== 200) {
            $this->error = "Open Library returned an error (HTTP $status).";
            return null;
        }
        return $body;
    }

    /** "June 1988" -> 1988 */
    private function year(string $date): ?int
    {
        return preg_match('/\d{4}/', $date, $m) ? (int) $m[0] : null;
    }
}
