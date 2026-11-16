<?php
namespace Spine;

/**
 * Router - matches a URL like /books/5 to a controller method
 * like BookController@show, and runs any middleware first.
 */
class Router
{
    private array $routes = [];
    private array $globalMiddleware = [];
    private array $middlewareAliases = [];

    public function get(string $path, array $action): self
    {
        return $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): self
    {
        return $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, array $action): self
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'action' => $action, 'middleware' => []];
        return $this;
    }

    /** Attach middleware to the route that was just added: ->middleware('auth') */
    public function middleware(string ...$names): self
    {
        $last = array_key_last($this->routes);
        $this->routes[$last]['middleware'] = array_merge($this->routes[$last]['middleware'], $names);
        return $this;
    }

    /** Give a middleware class a short name, e.g. 'auth' => AuthMiddleware::class */
    public function alias(string $name, string $class): void
    {
        $this->middlewareAliases[$name] = $class;
    }

    /** Middleware that runs on every request (e.g. logging). */
    public function useGlobal(string $class): void
    {
        $this->globalMiddleware[] = $class;
    }

    /** Turn '/books/{id}' into the regex '#^/books/(?P<id>[^/]+)$#' */
    private function toRegex(string $path): string
    {
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }
            if (!preg_match($this->toRegex($route['path']), $request->path(), $matches)) {
                continue;
            }

            // Keep only the named parts, e.g. ['id' => '5']
            $request->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // The final step: create the controller and call its method
            [$class, $method] = $route['action'];
            $core = function (Request $req) use ($class, $method) {
                $result = (new $class())->$method($req);
                return $result instanceof Response ? $result : Response::html((string) $result);
            };

            $names = array_merge($this->globalMiddleware, $route['middleware']);
            return $this->runPipeline($names, $request, $core);
        }

        $notFound = fn() => Response::html(View::render('errors/404', ['title' => 'Page not found']), 404);
        return $this->runPipeline($this->globalMiddleware, $request, $notFound);
    }

    /**
     * Wrap the controller in each middleware, like layers of an onion.
     * Each middleware decides whether to call $next (continue) or return early.
     */
    private function runPipeline(array $names, Request $request, callable $core): Response
    {
        $next = $core;
        foreach (array_reverse($names) as $name) {
            $class = $this->middlewareAliases[$name] ?? $name;
            $next = fn(Request $req) => (new $class())->handle($req, $next);
        }
        return $next($request);
    }
}
