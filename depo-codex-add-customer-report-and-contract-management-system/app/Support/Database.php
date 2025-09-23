<?php

namespace App\Support;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function initialize(): void
    {
        if (self::$connection !== null) {
            return;
        }

        $driver = env('DB_CONNECTION', 'sqlite');

        if ($driver !== 'sqlite') {
            throw new \RuntimeException('Only sqlite is supported in this environment.');
        }

        $database = env('DB_DATABASE', __DIR__ . '/../../storage/database.sqlite');

        if (!file_exists(dirname($database))) {
            mkdir(dirname($database), 0777, true);
        }

        if (!file_exists($database)) {
            touch($database);
        }

        try {
            self::$connection = new PDO('sqlite:' . $database);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::initialize();
        }

        return self::$connection;
    }
}
