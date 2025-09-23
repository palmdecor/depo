<?php

namespace App\Models;

use App\Support\Database;
use PDO;

abstract class Model
{
    protected static string $table;

    protected static function connection(): PDO
    {
        return Database::connection();
    }

    public static function create(array $attributes): int
    {
        $columns = array_keys($attributes);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        $stmt = self::connection()->prepare($sql);
        $stmt->execute($attributes);

        return (int) self::connection()->lastInsertId();
    }

    public static function findBy(array $criteria): ?array
    {
        $clauses = [];
        foreach ($criteria as $key => $value) {
            $clauses[] = $key . ' = :' . $key;
        }

        $sql = 'SELECT * FROM ' . static::$table . ' WHERE ' . implode(' AND ', $clauses) . ' LIMIT 1';
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($criteria);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function allBy(array $criteria = [], ?string $orderBy = null): array
    {
        $sql = 'SELECT * FROM ' . static::$table;
        $params = [];

        if (!empty($criteria)) {
            $clauses = [];
            foreach ($criteria as $key => $value) {
                $clauses[] = $key . ' = :' . $key;
                $params[$key] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        if ($orderBy) {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public static function update(int $id, array $attributes): void
    {
        $set = [];
        foreach ($attributes as $key => $value) {
            $set[] = $key . ' = :' . $key;
        }

        $attributes['id'] = $id;
        $sql = 'UPDATE ' . static::$table . ' SET ' . implode(',', $set) . ' WHERE id = :id';
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($attributes);
    }
}
