<?php

namespace App\Http;

class Request
{
    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = self::input($key);
        }
        return $data;
    }

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    public static function file(string $key): ?array
    {
        if (!isset($_FILES[$key])) {
            return null;
        }

        if (is_array($_FILES[$key]['error'])) {
            return null;
        }

        if ($_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        return $_FILES[$key];
    }
}
