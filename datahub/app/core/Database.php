<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConnection(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $this->config['host'], $this->config['name'], $this->config['charset']);

        try {
            $pdo = new PDO($dsn, $this->config['user'], $this->config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            die('Database connection failed: ' . $exception->getMessage());
        }

        return $pdo;
    }
}
