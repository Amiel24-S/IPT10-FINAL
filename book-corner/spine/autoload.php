<?php
/**
 * Autoloader: when PHP meets a class it doesn't know yet,
 * this turns the class name into a file path and loads it.
 *   Spine\Router            -> spine/Router.php
 *   App\Controllers\Home... -> app/Controllers/Home....php
 */
spl_autoload_register(function (string $class) {
    $prefixes = [
        'Spine\\' => __DIR__ . '/',
        'App\\'   => dirname(__DIR__) . '/app/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
    }
});
