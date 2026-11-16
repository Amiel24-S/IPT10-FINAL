<?php
namespace App\Middleware;

use Spine\Middleware;
use Spine\Request;
use Spine\Response;
use Spine\Session;

/** Only logged-in users may continue. Everyone else goes to /login. */
class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Session::get('user')) {
            Session::flash('Please log in first.', 'error');
            return Response::redirect('/login');
        }
        return $next($request);
    }
}
