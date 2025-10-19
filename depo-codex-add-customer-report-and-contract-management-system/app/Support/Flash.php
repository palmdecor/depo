<?php

namespace App\Support;

class Flash
{
    private const SESSION_KEY = '_flash_message';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('danger', $message);
    }

    public static function pull(): ?array
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            return null;
        }

        $message = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);

        return $message;
    }
}
