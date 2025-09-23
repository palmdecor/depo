<?php

namespace App\Support;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            self::regenerate();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function tokenField(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    public static function verify(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $valid = isset($_SESSION[self::SESSION_KEY]) && hash_equals($_SESSION[self::SESSION_KEY], $token);
        if ($valid) {
            self::regenerate();
        }

        return $valid;
    }

    public static function regenerate(): void
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }
}
