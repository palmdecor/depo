<?php

use App\Support\SessionManager;

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return SessionManager::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('flash_errors')) {
    function flash_errors(): array
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }
}

if (!function_exists('flash_old')) {
    function flash_old(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        return $old;
    }
}

if (!function_exists('flash_success')) {
    function flash_success(): ?string
    {
        $message = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        return $message;
    }
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return \App\Support\Config::get($key, $default);
    }
}
