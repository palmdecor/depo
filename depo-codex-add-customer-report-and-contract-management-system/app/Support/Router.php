<?php

namespace App\Support;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $normalized = $this->normalize($path);

        if ($method === 'POST') {
            $token = $_POST['_token'] ?? null;
            if (!\App\Support\SessionManager::verifyCsrfToken($token)) {
                http_response_code(419);
                return 'CSRF token mismatch.';
            }

            \App\Support\SessionManager::regenerateCsrfToken();
        }

        $handler = $this->routes[$method][$normalized] ?? null;

        if ($handler === null) {
            http_response_code(404);
            return '404 Not Found';
        }

        return (string) $handler();
    }

    private function normalize(string $path): string
    {
        return rtrim($path, '/') ?: '/';
    }
}
