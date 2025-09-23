<?php

namespace App\Controllers;

use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\View;

class DashboardController
{
    public function __construct(
        private AuthService $auth,
        private ReportRepository $reports,
        private ContractSubmissionRepository $contractSubmissions,
        private UserRepository $users
    ) {
    }

    public function index(): string
    {
        $user = $this->auth->requireUser();

        if ($this->auth->isAdmin($user)) {
            $customers = $this->users->allCustomers();
            $blocked = array_filter($customers, fn($customer) => (int) $customer['is_blocked'] === 1);

            return View::render('admin/dashboard', [
                'user' => $user,
                'stats' => [
                    'total_customers' => count($customers),
                    'blocked_customers' => count($blocked),
                    'recent_customers' => array_slice($customers, 0, 5),
                ],
            ]);
        }

        $reports = $this->reports->getByUser((int) $user['id']);
        $contracts = $this->contractSubmissions->getByUser((int) $user['id']);

        return View::render('customer/dashboard', [
            'user' => $user,
            'reports' => array_slice($reports, 0, 5),
            'contracts' => array_slice($contracts, 0, 5),
        ]);
    }
}
