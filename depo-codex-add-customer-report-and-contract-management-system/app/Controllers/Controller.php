<?php

namespace App\Controllers;

use App\Http\Response;
use App\Services\AuthService;

abstract class Controller
{
    public function __construct(protected AuthService $auth)
    {
    }

    protected function requireUser(): array
    {
        $user = $this->auth->user();

        if (!$user) {
            Response::redirect('/login');
        }

        if ((int) ($user['is_blocked'] ?? 0) === 1) {
            $this->auth->logout();
            Response::redirect('/login');
        }

        return $user;
    }

    protected function requireAdmin(): array
    {
        $user = $this->requireUser();
        if (($user['role'] ?? 'customer') !== 'admin') {
            Response::redirect('/dashboard');
        }

        return $user;
    }
}
