<?php

namespace App\Support;

class View
{
    public static function render(string $template, array $data = []): string
    {
        $path = __DIR__ . '/../../resources/views/' . $template . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("View {$template} not found.");
        }

        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
