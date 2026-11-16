<?php
namespace Spine;

/**
 * Controller - parent class for all controllers.
 * Gives them short helpers: view(), redirect(), user().
 */
abstract class Controller
{
    protected function view(string $template, array $data = []): Response
    {
        return Response::html(View::render($template, $data));
    }

    protected function redirect(string $path, ?string $message = null, string $type = 'success'): Response
    {
        if ($message) {
            Session::flash($message, $type);
        }
        return Response::redirect($path);
    }

    protected function user(): ?array
    {
        return Session::get('user');
    }
}
