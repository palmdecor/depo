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

        try {
            if ($driver === 'mysql') {
                $host = env('DB_HOST', '127.0.0.1');
                $port = env('DB_PORT', '3306');
                $database = env('DB_DATABASE');
                $username = env('DB_USERNAME');
                $password = env('DB_PASSWORD');

                if (!$database || !$username) {
                    throw new \RuntimeException('MySQL bağlantısı için DB_DATABASE ve DB_USERNAME alanları gereklidir.');
                }

                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
                self::$connection = new PDO($dsn, $username, $password ?? '');
            } else {
                $database = env('DB_DATABASE', __DIR__ . '/../../storage/database.sqlite');

                if (!file_exists(dirname($database))) {
                    mkdir(dirname($database), 0777, true);
                }

                if (!file_exists($database)) {
                    touch($database);
                }

                self::$connection = new PDO('sqlite:' . $database);
            }

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

    public static function driver(): string
    {
        return env('DB_CONNECTION', 'sqlite');
    }
}
