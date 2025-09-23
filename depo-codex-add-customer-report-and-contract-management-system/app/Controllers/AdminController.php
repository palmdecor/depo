<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\ContractService;
use App\Services\ReportService;
use App\Support\Validator;
use App\Support\View;

class AdminController
{
    public function __construct(
        private AuthService $auth,
        private UserRepository $users,
        private ReportRepository $reports,
        private ContractSubmissionRepository $contracts,
        private ReportService $reportService,
        private ContractService $contractService
    ) {
    }

    private function ensureAdmin(): array
    {
        $user = $this->auth->requireUser();
        if (!$this->auth->isAdmin($user)) {
            Response::redirect('/dashboard');
        }

        return $user;
    }

    public function customers(): string
    {
        $this->ensureAdmin();
        $query = Request::input('q');
        $customers = $this->users->allCustomers($query);
        $success = flash_success();

        return View::render('admin/customers/index', [
            'customers' => $customers,
            'query' => $query,
            'success' => $success,
        ]);
    }

    public function showCustomer(): string
    {
        $this->ensureAdmin();
        $id = (int) (Request::input('id') ?? 0);
        $customer = $this->users->find($id);

        if (!$customer || ($customer['role'] ?? '') !== 'customer') {
            http_response_code(404);
            return 'Müşteri bulunamadı.';
        }

        $reports = $this->reports->getByUser($id);
        $contracts = $this->contracts->getByUser($id);
        $errors = flash_errors();
        $success = flash_success();

        return View::render('admin/customers/edit', [
            'customer' => $customer,
            'reports' => $reports,
            'contracts' => $contracts,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function updateStatus(): void
    {
        $this->ensureAdmin();
        $userId = (int) (Request::input('user_id') ?? 0);
        $action = Request::input('action');

        $customer = $this->users->find($userId);
        if (!$customer || ($customer['role'] ?? '') !== 'customer') {
            $_SESSION['errors'] = ['general' => ['Müşteri bulunamadı.']];
            Response::redirect('/admin/customers');
        }

        $blocked = $action === 'block';
        $this->users->toggleBlock($userId, $blocked);

        $_SESSION['success'] = $blocked ? 'Müşteri hesabı engellendi.' : 'Müşteri hesabı aktifleştirildi.';
        Response::redirect('/admin/customers/edit?id=' . $userId);
    }

    public function uploadReport(): void
    {
        $this->ensureAdmin();
        $userId = (int) (Request::input('user_id') ?? 0);
        $customer = $this->users->find($userId);

        if (!$customer || ($customer['role'] ?? '') !== 'customer') {
            $_SESSION['errors'] = ['general' => ['Müşteri bulunamadı.']];
            Response::redirect('/admin/customers');
        }

        $file = Request::file('report');

        if (!$file) {
            $_SESSION['errors'] = ['report' => ['Lütfen bir PDF dosyası seçiniz.']];
            Response::redirect('/admin/customers/edit?id=' . $userId);
        }

        try {
            $this->reportService->uploadForUser($userId, $file);
            $_SESSION['success'] = 'Rapor başarıyla yüklendi.';
        } catch (\Throwable $e) {
            $_SESSION['errors'] = ['report' => [$e->getMessage()]];
        }

        Response::redirect('/admin/customers/edit?id=' . $userId);
    }

    public function showContractTemplate(): string
    {
        $this->ensureAdmin();
        $errors = flash_errors();
        $success = flash_success();
        $old = flash_old();
        $template = $this->contractService->getTemplate();

        return View::render('admin/contracts/template', [
            'content' => $old['content'] ?? ($template['content'] ?? ''),
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function updateContractTemplate(): void
    {
        $this->ensureAdmin();
        $content = (string) Request::input('content');

        $errors = Validator::validate(['content' => $content], [
            'content' => 'required|min:50',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['content' => $content];
            Response::redirect('/admin/contracts/template');
        }

        $this->contractService->saveTemplate($content);
        $_SESSION['success'] = 'Sözleşme şablonu güncellendi.';
        Response::redirect('/admin/contracts/template');
    }
}
