<?php

namespace App\Support;

class Config
{
    private static array $items = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $value = trim($value, "\"' ");
            self::$items[$key] = $value;
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$items[$key] ?? $default;
    }
}
