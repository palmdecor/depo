<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\ReportService;
use App\Support\Csrf;
use App\Support\View;
use RuntimeException;

class CustomerController extends Controller
{
    public function __construct(
        AuthService $auth,
        private UserRepository $users,
        private ReportRepository $reports,
        private ReportService $reportService
    ) {
        parent::__construct($auth);
    }

    public function index(): string
    {
        $this->requireAdmin();
        $search = trim((string) (Request::input('q') ?? ''));
        $customers = $this->users->customers($search ?: null);
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['errors'], $_SESSION['success']);

        return View::render('admin/customers/index', [
            'customers' => $customers,
            'search' => $search,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function show(): string
    {
        $admin = $this->requireAdmin();
        $id = (int) (Request::input('id') ?? 0);
        if ($id <= 0) {
            Response::redirect('/admin/customers');
        }

        $customer = $this->users->find($id);
        if (!$customer || ($customer['role'] ?? 'customer') !== 'customer') {
            Response::redirect('/admin/customers');
        }

        $reports = $this->reports->forUser($id);
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['errors'], $_SESSION['success']);

        return View::render('admin/customers/edit', [
            'admin' => $admin,
            'customer' => $customer,
            'reports' => $reports,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function toggleStatus(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            exit('Geçersiz oturum doğrulaması.');
        }

        $this->requireAdmin();
        $id = (int) (Request::input('id') ?? 0);
        if ($id <= 0) {
            Response::redirect('/admin/customers');
        }

        $customer = $this->users->find($id);
        if (!$customer || ($customer['role'] ?? 'customer') !== 'customer') {
            Response::redirect('/admin/customers');
        }

        $shouldBlock = (Request::input('action') === 'block');
        $this->users->setBlocked($id, $shouldBlock);
        $_SESSION['success'] = $shouldBlock ? 'Müşteri hesabı engellendi.' : 'Müşteri hesabı aktifleştirildi.';
        Response::redirect('/admin/customers/edit?id=' . $id);
    }

    public function uploadReport(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            exit('Geçersiz oturum doğrulaması.');
        }

        $admin = $this->requireAdmin();
        $id = (int) (Request::input('id') ?? 0);
        if ($id <= 0) {
            Response::redirect('/admin/customers');
        }

        $customer = $this->users->find($id);
        if (!$customer || ($customer['role'] ?? 'customer') !== 'customer') {
            Response::redirect('/admin/customers');
        }

        $file = $_FILES['report'] ?? null;
        if (!$file) {
            $_SESSION['errors'] = ['report' => ['Lütfen bir rapor dosyası seçin.']];
            Response::redirect('/admin/customers/edit?id=' . $id);
        }

        try {
            $this->reportService->uploadForUser($id, $file, (int) $admin['id']);
            $_SESSION['success'] = 'Rapor başarıyla yüklendi.';
        } catch (RuntimeException $exception) {
            $_SESSION['errors'] = ['report' => [$exception->getMessage()]];
        }

        Response::redirect('/admin/customers/edit?id=' . $id);
    }
}
