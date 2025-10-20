<?php
namespace App\Core;

class View
{
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function render(string $template, array $data = [], string $layout = 'layouts/main.php'): void
    {
        $view = $this;
        $content = function () use ($template, $data, $view) {
            extract($data);
            $view = $view;
            include __DIR__ . '/../views/' . $template;
        };

        if ($layout) {
            extract($data);
            $view = $view;
            include __DIR__ . '/../views/' . $layout;
        } else {
            $content();
        }
    }

    public function asset(string $path): string
    {
        return $this->baseUrl . '/public/' . ltrim($path, '/');
    }

    public function url(string $path = ''): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
