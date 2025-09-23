<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;
use App\Support\Validator;
use App\Support\View;

class AuthController
{
    public function __construct(private AuthService $auth)
    {
    }

    public function showRegister(): string
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        return View::render('auth/register', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function register(): string
    {
        $data = Request::only(['first_name', 'last_name', 'phone', 'email', 'password', 'password_confirmation']);

        $errors = Validator::validate($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($this->auth->user()) {
            Response::redirect('/dashboard');
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $old = $data;
            unset($old['password'], $old['password_confirmation']);
            $_SESSION['old'] = $old;
            Response::redirect('/register');
        }

        if ($this->auth->emailExists($data['email'])) {
            $_SESSION['errors'] = ['email' => ['Bu e-posta adresi zaten kayıtlı.']];
            $old = $data;
            unset($old['password'], $old['password_confirmation']);
            $_SESSION['old'] = $old;
            Response::redirect('/register');
        }

        unset($_SESSION['errors'], $_SESSION['old']);

        $user = $this->auth->register($data);
        $this->auth->loginUser($user);

        Response::redirect('/dashboard');
        return '';
    }

    public function showLogin(): string
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        return View::render('auth/login', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function login(): string
    {
        $data = Request::only(['email', 'password']);

        $errors = Validator::validate($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $old = $data;
            unset($old['password']);
            $_SESSION['old'] = $old;
            Response::redirect('/login');
        }

        unset($_SESSION['errors'], $_SESSION['old']);

        if (!$this->auth->attempt($data)) {
            $_SESSION['errors'] = ['general' => ['Kimlik doğrulama başarısız.']];
            $old = $data;
            unset($old['password']);
            $_SESSION['old'] = $old;
            Response::redirect('/login');
        }

        Response::redirect('/dashboard');
        return '';
    }

    public function logout(): void
    {
        $this->auth->logout();
        Response::redirect('/login');
    }
}
