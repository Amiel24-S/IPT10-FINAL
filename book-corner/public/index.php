<?php
/**
 * Front controller: every request to Book Corner starts here.
 * .htaccess sends all URLs to this file.
 */

// When using "php -S", let the built-in server send real files (CSS, images) directly
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require __DIR__ . '/../spine/autoload.php';

$app = new Spine\App(dirname(__DIR__));

$router = $app->router;
require dirname(__DIR__) . '/config/routes.php';

$app->run();
