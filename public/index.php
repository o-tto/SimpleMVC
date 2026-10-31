<?php

declare(strict_types=1);

use mvcsystem\RequestDirector;

require dirname(__DIR__) . '/vendor/autoload.php';

// When using PHP's built-in server, let it serve existing static files directly.
if (PHP_SAPI === 'cli-server') {
    $file = realpath(__DIR__ . rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    if ($file !== false && is_file($file) && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR) && $file !== __FILE__) {
        return false;
    }
}

$debug = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL);
ini_set('display_errors', $debug ? '1' : '0');

(new RequestDirector(debug: $debug))->httpRequest();
