<?php

use App\Controllers\Admin\ContractTemplateController;
use App\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Controllers\AuthController;
use App\Controllers\Customer\ContractController as CustomerContractController;
use App\Controllers\Customer\PasswordController as CustomerPasswordController;
use App\Controllers\Customer\ReportController as CustomerReportController;
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
$reportService = new ReportService($reportRepository);
$contractService = new ContractService($contractTemplateRepository, $contractSubmissionRepository);

$authController = new AuthController($authService);
$dashboardController = new DashboardController($authService, $userRepository, $reportRepository);
$customerReportController = new CustomerReportController($authService, $reportRepository);
$customerPasswordController = new CustomerPasswordController($authService, $userRepository);
$customerContractController = new CustomerContractController($authService, $contractService);
$adminCustomerController = new AdminCustomerController($authService, $userRepository, $reportRepository, $reportService);
$contractTemplateController = new ContractTemplateController($authService, $contractService);
$homeController = new HomeController();

$router->get('/', fn() => $homeController->index());
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->get('/register', fn() => $authController->showRegister());
$router->post('/register', fn() => $authController->register());
$router->get('/dashboard', fn() => $dashboardController->index());
$router->get('/customer/reports', fn() => $customerReportController->index());
$router->get('/customer/reports/download', function () use ($customerReportController) {
    $customerReportController->download();
    return '';
});
$router->get('/customer/password', fn() => $customerPasswordController->show());
$router->post('/customer/password', function () use ($customerPasswordController) {
    $customerPasswordController->update();
    return '';
});
$router->get('/customer/contract', fn() => $customerContractController->show());
$router->post('/customer/contract', function () use ($customerContractController) {
    $customerContractController->generate();
    return '';
});
$router->get('/admin/customers', fn() => $adminCustomerController->index());
$router->get('/admin/customers/edit', fn() => $adminCustomerController->show());
$router->post('/admin/customers/status', function () use ($adminCustomerController) {
    $adminCustomerController->toggleStatus();
    return '';
});
$router->post('/admin/customers/upload-report', function () use ($adminCustomerController) {
    $adminCustomerController->uploadReport();
    return '';
});
$router->get('/admin/contract-template', fn() => $contractTemplateController->show());
$router->post('/admin/contract-template', function () use ($contractTemplateController) {
    $contractTemplateController->update();
    return '';
});
$router->get('/logout', fn() => $authController->logout());

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
