<?php

namespace App\Controllers;

use App\Http\Response;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Support\View;

class DashboardController
{
    private AuthService $auth;
    private CustomerService $customers;

    public function __construct(AuthService $auth, CustomerService $customers)
    {
        $this->auth = $auth;
        $this->customers = $customers;
    }

    public function index(): string
    {
        $user = $this->auth->user();

        if (!$user) {
            Response::redirect('/login');
        }

        $windowDays = 30;
        $upcoming = $this->customers->expiringWithin($windowDays);

        return View::render('customer/dashboard', [
            'user' => $user,
            'upcoming' => $upcoming,
            'windowDays' => $windowDays,
        ]);
    }
}
