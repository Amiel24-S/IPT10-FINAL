<?php
namespace App\Middleware;

use Spine\Middleware;
use Spine\Request;
use Spine\Response;
use Spine\Session;

/** Every POST form must send back the secret token from csrf_field(). */
class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if ($request->isPost() && !hash_equals(Session::token(), (string) $request->input('_token'))) {
            return Response::html('<h1>419 - Form expired</h1><p>Please go back and try again.</p>', 419);
        }
        return $next($request);
    }
}
