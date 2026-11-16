<?php
namespace App\Middleware;

use Spine\Middleware;
use Spine\Request;
use Spine\Response;
use Spine\Session;

/** Only users with role = 'admin' may open the admin pages. */
class AdminMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            Session::flash('Admins only.', 'error');
            return Response::redirect('/login');
        }
        return $next($request);
    }
}
