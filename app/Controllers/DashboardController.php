<?php

namespace App\Controllers;

use App\Http\Response;
use App\Services\AuthService;
use App\Support\View;

class DashboardController
{
    public function __construct(private AuthService $auth)
    {
    }

    public function index(): string
    {
        $user = $this->auth->user();

        if (!$user) {
            \App\Http\Response::redirect('/login');
        }

        return View::render('customer/dashboard', [
            'user' => $user,
        ]);
    }
}
