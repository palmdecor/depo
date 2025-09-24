<?php

namespace App\Http;

class Response
{
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
