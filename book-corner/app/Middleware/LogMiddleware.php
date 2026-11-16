<?php
namespace App\Middleware;

use Spine\Middleware;
use Spine\Request;
use Spine\Response;

/** Writes one line per request to storage/logs/app.log */
class LogMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $ms = round((microtime(true) - $start) * 1000);

        $line = sprintf("[%s] %s %s -> %d (%dms)\n",
            date('Y-m-d H:i:s'), $request->method(), $request->path(), $response->status, $ms);
        @file_put_contents(BASE_PATH . '/storage/logs/app.log', $line, FILE_APPEND);

        return $response;
    }
}
