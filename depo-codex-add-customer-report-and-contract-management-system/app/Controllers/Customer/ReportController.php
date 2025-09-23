<?php

namespace App\Controllers\Customer;

use App\Controllers\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\ReportRepository;
use App\Services\AuthService;
use App\Support\View;

class ReportController extends Controller
{
    public function __construct(AuthService $auth, private ReportRepository $reports)
    {
        parent::__construct($auth);
    }

    public function index(): string
    {
        $user = $this->requireUser();
        $reports = $this->reports->forUser((int) $user['id']);
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success'], $_SESSION['errors']);

        return View::render('customer/reports', [
            'user' => $user,
            'reports' => $reports,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function download(): void
    {
        $user = $this->requireUser();
        $reportId = (int) (Request::input('id') ?? 0);
        if ($reportId <= 0) {
            Response::redirect('/customer/reports');
        }

        $report = $this->reports->find($reportId);
        if (!$report) {
            Response::redirect('/customer/reports');
        }

        if (($user['role'] ?? 'customer') !== 'admin' && (int) $report['user_id'] !== (int) $user['id']) {
            Response::redirect('/customer/reports');
        }

        if (!is_file($report['file_path'])) {
            $_SESSION['errors'] = ['general' => ['Dosya bulunamadı.']];
            Response::redirect('/customer/reports');
        }

        Response::download($report['file_path'], $report['original_name']);
    }
}
