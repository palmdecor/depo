<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\Router;

require __DIR__ . '/../bootstrap.php';

$router = new Router();
$authService = new AuthService(new UserRepository());
$authController = new AuthController($authService);
$dashboardController = new DashboardController($authService);
$homeController = new HomeController();

$router->get('/', fn() => $homeController->index());
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->get('/register', fn() => $authController->showRegister());
$router->post('/register', fn() => $authController->register());
$router->get('/dashboard', fn() => $dashboardController->index());
$router->get('/logout', fn() => $authController->logout());

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
