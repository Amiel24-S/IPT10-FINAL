<?php
namespace Spine;

/**
 * View - turns a PHP template into HTML.
 * The page is rendered first, then placed inside layout.php.
 */
class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = self::capture(BASE_PATH . "/app/Views/$template.php", $data);
        if ($layout === null) {
            return $content;
        }
        return self::capture(BASE_PATH . "/app/Views/$layout.php", array_merge($data, ['content' => $content]));
    }

    private static function capture(string $file, array $data): string
    {
        extract($data);          // ['books' => [...]] becomes $books
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
