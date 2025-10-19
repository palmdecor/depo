<?php

use App\Support\Config;
use App\Support\Database;
use App\Support\SessionManager;

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $path = __DIR__ . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($path)) {
            require $path;
        }
    });
}

require_once __DIR__ . '/app/Support/helpers.php';

Config::load(__DIR__ . '/.env');

Database::initialize();
SessionManager::start();
