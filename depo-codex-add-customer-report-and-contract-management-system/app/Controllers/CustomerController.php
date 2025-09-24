<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ReportRepository;
use App\Services\AuthService;
use App\Services\ContractService;
use App\Support\Validator;
use App\Support\View;

class CustomerController
{
    public function __construct(
        private AuthService $auth,
        private ReportRepository $reports,
        private ContractSubmissionRepository $contracts,
        private ContractService $contractService
    ) {
    }

    public function reports(): string
    {
        $user = $this->auth->requireUser();

        if (!$this->auth->isCustomer($user)) {
            Response::redirect('/dashboard');
        }

        $reports = $this->reports->getByUser((int) $user['id']);
        $errors = flash_errors();
        $success = flash_success();

        return View::render('customer/reports', [
            'user' => $user,
            'reports' => $reports,
            'success' => $success,
            'errors' => $errors,
        ]);
    }

    public function downloadReport(): void
    {
        $user = $this->auth->requireUser();
        $reportId = (int) (Request::input('id') ?? 0);
        $report = $this->reports->find($reportId);

        if (!$report) {
            http_response_code(404);
            echo 'Rapor bulunamadı.';
            return;
        }

        if (!$this->auth->isAdmin($user) && (int) $report['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'Bu rapora erişim yetkiniz yok.';
            return;
        }

        $path = __DIR__ . '/../../public/' . $report['stored_name'];

        if (!file_exists($path)) {
            http_response_code(404);
            echo 'Dosya bulunamadı.';
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($report['original_name']) . '"');
        header('Content-Length: ' . (string) filesize($path));
        readfile($path);
        exit;
    }

    public function showPasswordForm(): string
    {
        $user = $this->auth->requireUser();
        $errors = flash_errors();
        $success = flash_success();

        return View::render('customer/password', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function updatePassword(): void
    {
        $user = $this->auth->requireUser();

        $data = [
            'current_password' => Request::input('current_password'),
            'password' => Request::input('password'),
            'password_confirmation' => Request::input('password_confirmation'),
        ];

        $errors = Validator::validate($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            Response::redirect('/password');
        }

        if (!password_verify((string) $data['current_password'], $user['password'])) {
            $_SESSION['errors'] = ['current_password' => ['Mevcut şifreniz yanlış.']];
            Response::redirect('/password');
        }

        $this->auth->updatePassword((int) $user['id'], (string) $data['password']);
        $_SESSION['success'] = 'Şifreniz başarıyla güncellendi.';

        Response::redirect('/password');
    }

    public function showContractForm(): string
    {
        $user = $this->auth->requireUser();

        if (!$this->auth->isCustomer($user)) {
            Response::redirect('/dashboard');
        }

        $errors = flash_errors();
        $old = flash_old();
        $template = $this->contractService->getTemplate();

        return View::render('customer/contract', [
            'user' => $user,
            'errors' => $errors,
            'old' => $old,
            'template' => $template,
        ]);
    }

    public function submitContract(): void
    {
        $user = $this->auth->requireUser();

        if (!$this->auth->isCustomer($user)) {
            Response::redirect('/dashboard');
        }

        $data = [
            'first_name' => (string) Request::input('first_name'),
            'last_name' => (string) Request::input('last_name'),
            'address' => (string) Request::input('address'),
            'national_id' => (string) Request::input('national_id'),
            'phone' => (string) Request::input('phone'),
            'email' => (string) Request::input('email'),
            'agree' => Request::input('agree'),
        ];

        $data = array_map(static fn($value) => is_string($value) ? trim($value) : $value, $data);

        $errors = Validator::validate($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'address' => 'required|min:10',
            'national_id' => 'required|digits:11',
            'phone' => 'required|min:10|max:20',
            'email' => 'required|email',
            'agree' => 'accepted',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            Response::redirect('/contract');
        }

        try {
            $file = $this->contractService->createSubmission($user, $data);
        } catch (\Throwable $e) {
            $_SESSION['errors'] = ['general' => [$e->getMessage()]];
            $_SESSION['old'] = $data;
            Response::redirect('/contract');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file['download_name'] . '"');
        header('Content-Length: ' . (string) filesize($file['full_path']));
        readfile($file['full_path']);
        exit;
    }
}
