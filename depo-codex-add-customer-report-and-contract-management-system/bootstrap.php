<?php

use App\Support\Config;
use App\Support\Database;
use App\Support\SessionManager;

$autoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    throw new RuntimeException('Autoload dosyası bulunamadı. Lütfen `composer install` komutunu çalıştırın.');
}

require $autoload;

Config::load(__DIR__ . '/.env');

Database::initialize();
SessionManager::start();
