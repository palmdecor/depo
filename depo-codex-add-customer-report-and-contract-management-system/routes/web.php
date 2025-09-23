<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ContractTemplateRepository;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\ContractService;
use App\Services\ReportService;
use App\Support\Router;

require __DIR__ . '/../bootstrap.php';

$router = new Router();
$userRepository = new UserRepository();
$reportRepository = new ReportRepository();
$contractTemplateRepository = new ContractTemplateRepository();
$contractSubmissionRepository = new ContractSubmissionRepository();
$authService = new AuthService($userRepository);
$contractService = new ContractService($contractTemplateRepository, $contractSubmissionRepository);
$reportService = new ReportService($reportRepository);

$authController = new AuthController($authService);
$dashboardController = new DashboardController($authService, $reportRepository, $contractSubmissionRepository, $userRepository);
$customerController = new CustomerController($authService, $reportRepository, $contractSubmissionRepository, $contractService);
$adminController = new AdminController($authService, $userRepository, $reportRepository, $contractSubmissionRepository, $reportService, $contractService);
$homeController = new HomeController();

$router->get('/', fn() => $homeController->index());
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->get('/register', fn() => $authController->showRegister());
$router->post('/register', fn() => $authController->register());
$router->get('/dashboard', fn() => $dashboardController->index());
$router->get('/reports', fn() => $customerController->reports());
$router->get('/reports/download', fn() => $customerController->downloadReport());
$router->get('/password', fn() => $customerController->showPasswordForm());
$router->post('/password', fn() => $customerController->updatePassword());
$router->get('/contract', fn() => $customerController->showContractForm());
$router->post('/contract', fn() => $customerController->submitContract());
$router->get('/admin/customers', fn() => $adminController->customers());
$router->get('/admin/customers/edit', fn() => $adminController->showCustomer());
$router->post('/admin/customers/status', fn() => $adminController->updateStatus());
$router->post('/admin/reports/upload', fn() => $adminController->uploadReport());
$router->get('/admin/contracts/template', fn() => $adminController->showContractTemplate());
$router->post('/admin/contracts/template', fn() => $adminController->updateContractTemplate());
$router->get('/logout', fn() => $authController->logout());

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
