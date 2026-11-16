<?php
namespace Spine;

/**
 * Every middleware class must have a handle() method.
 * Call $next($request) to let the request continue,
 * or return a Response to stop it (e.g. redirect to login).
 */
interface Middleware
{
    public function handle(Request $request, callable $next): Response;
}
