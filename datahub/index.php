<?php
session_start();

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Bağımlılıklar eksik. Lütfen "composer install" komutunu çalıştırın.');
}

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

use App\Core\App;

$app = new App($config);
$app->run();
