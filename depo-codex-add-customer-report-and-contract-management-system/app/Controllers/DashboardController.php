<?php

namespace App\Controllers;

use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\View;

class DashboardController extends Controller
{
    public function __construct(AuthService $auth, private UserRepository $users, private ReportRepository $reports)
    {
        parent::__construct($auth);
    }

    public function index(): string
    {
        $user = $this->requireUser();

        if (($user['role'] ?? 'customer') === 'admin') {
            return View::render('admin/dashboard', [
                'user' => $user,
                'customerCount' => $this->users->countByRole('customer'),
                'blockedCount' => $this->users->countBlockedCustomers(),
                'reportCount' => $this->reports->totalCount(),
            ]);
        }

        return View::render('customer/dashboard', [
            'user' => $user,
        ]);
    }
}
