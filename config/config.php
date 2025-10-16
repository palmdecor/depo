<?php
// config/config.php

declare(strict_types=1);

const ROOT_PATH = __DIR__ . '/..';

$envFile = ROOT_PATH . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        [$name, $value] = array_map('trim', explode('=', $line, 2) + [1 => '']);
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv($name . '=' . $value);
        }
    }
}

define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('DB_DSN', $_ENV['DB_DSN'] ?? 'pgsql:host=localhost;port=5432;dbname=kredi');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY'] ?? '');
define('OPENAI_MODEL', $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini');
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'change_me');
define('JWT_TTL', (int)($_ENV['JWT_TTL'] ?? 3600));
