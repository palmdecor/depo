<?php

use App\Support\Config;
use App\Support\Database;
use App\Support\SessionManager;

require __DIR__ . '/vendor/autoload.php';

Config::load(__DIR__ . '/.env');

Database::initialize();
SessionManager::start();
