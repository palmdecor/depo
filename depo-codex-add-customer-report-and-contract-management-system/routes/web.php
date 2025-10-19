<?php

use App\Controllers\AuthController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Support\Router;

require __DIR__ . '/../bootstrap.php';

$router = new Router();
$authService = new AuthService(new UserRepository());
$customerRepository = new CustomerRepository();
$customerService = new CustomerService($customerRepository);
$authController = new AuthController($authService);
$dashboardController = new DashboardController($authService, $customerService);
$customerController = new CustomerController($authService, $customerService);
$homeController = new HomeController();

$router->get('/', fn() => $homeController->index());
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->get('/register', fn() => $authController->showRegister());
$router->post('/register', fn() => $authController->register());
$router->get('/dashboard', fn() => $dashboardController->index());
$router->get('/customers', fn() => $customerController->index());
$router->get('/customers/create', fn() => $customerController->create());
$router->post('/customers', fn() => $customerController->store());
$router->get('/customers/edit', fn() => $customerController->edit());
$router->post('/customers/update', fn() => $customerController->update());
$router->post('/customers/delete', fn() => $customerController->destroy());
$router->get('/logout', fn() => $authController->logout());

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
