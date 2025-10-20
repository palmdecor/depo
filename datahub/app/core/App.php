<?php
namespace App\Core;

class App
{
    protected array $config;
    protected string $controller = 'AuthController';
    protected string $method = 'login';
    protected array $params = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        Registry::set('config', $config);
        Registry::set('session', new Session());
        Registry::set('db', (new Database($config['db']))->getConnection());
        Registry::set('auth', new Auth());
        Registry::set('view', new View($config['base_url'] ?? ''));
        Registry::set('settings', new SettingsService());
    }

    public function run(): void
    {
        $url = $this->parseUrl();

        if (!empty($url[0])) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        $controllerClass = '\\App\\Controllers\\' . $this->controller;

        if (!class_exists($controllerClass)) {
            $controllerClass = '\\App\\Controllers\\AuthController';
            $this->controller = 'AuthController';
        }

        $controller = new $controllerClass();

        if (!empty($url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        if (!method_exists($controller, $this->method)) {
            $this->method = 'index';
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$controller, $this->method], $this->params);
    }

    protected function parseUrl(): array
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return explode('/', $url);
    }
}
