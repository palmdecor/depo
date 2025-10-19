<?php

use App\Support\Config;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Config::get($key, $default);
    }
}
