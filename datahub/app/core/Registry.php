<?php
namespace App\Core;

class Registry
{
    private static array $container = [];

    public static function set(string $key, $value): void
    {
        self::$container[$key] = $value;
    }

    public static function get(string $key)
    {
        return self::$container[$key] ?? null;
    }
}
