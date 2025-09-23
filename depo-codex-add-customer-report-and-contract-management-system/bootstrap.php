<?php

use App\Support\Config;
use App\Support\Database;
use App\Support\SessionManager;

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = __DIR__ . '/app/' . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    });

    require_once __DIR__ . '/app/Support/helpers.php';
}

Config::load(__DIR__ . '/.env');

Database::initialize();
SessionManager::start();
