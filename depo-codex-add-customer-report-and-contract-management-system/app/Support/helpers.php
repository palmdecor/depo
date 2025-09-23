<?php

use App\Support\Config;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = Config::get($key);
        return $value !== null ? $value : $default;
    }
}

if (!function_exists('now')) {
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
