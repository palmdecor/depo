<?php
namespace App\Core;

class Controller
{
    protected View $view;
    protected Auth $auth;
    protected Session $session;

    public function __construct()
    {
        $this->view = Registry::get('view');
        $this->auth = Registry::get('auth');
        $this->session = Registry::get('session');
    }

    protected function redirect(string $path): void
    {
        $config = Registry::get('config');
        $base = rtrim($config['base_url'] ?? '', '/');
        header('Location: ' . $base . '/' . ltrim($path, '/'));
        exit;
    }

    protected function ensureRole(array $roles): void
    {
        $user = $this->auth->user();
        if (!$user || !in_array($user['role'], $roles, true)) {
            $this->redirect('auth/login');
        }
    }
}
