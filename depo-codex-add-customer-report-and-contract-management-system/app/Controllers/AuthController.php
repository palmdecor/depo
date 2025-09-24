<?php

namespace App\Controllers;

use App\Exceptions\IdentityVerificationException;
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
        $errors = flash_errors();
        $old = flash_old();

        return View::render('auth/register', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function register(): string
    {
        $data = Request::only([
            'first_name',
            'last_name',
            'phone',
            'email',
            'national_id',
            'birth_year',
            'password',
            'password_confirmation',
        ]);
        $data = array_map(static fn($value) => is_string($value) ? trim($value) : $value, $data);

        if (isset($data['national_id'])) {
            $data['national_id'] = preg_replace('/\D+/', '', (string) $data['national_id']);
        }

        if (isset($data['birth_year'])) {
            $data['birth_year'] = preg_replace('/\D+/', '', (string) $data['birth_year']);
        }

        $errors = Validator::validate($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'national_id' => 'required|digits:11',
            'birth_year' => 'required|digits:4',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!empty($data['birth_year'])) {
            $birthYear = (int) $data['birth_year'];
            $currentYear = (int) date('Y');
            if ($birthYear < 1900 || $birthYear > $currentYear) {
                $errors['birth_year'][] = 'Geçerli bir doğum yılı giriniz.';
            }
        }

        if (!empty($data['national_id']) && !preg_match('/^[1-9][0-9]{10}$/', (string) $data['national_id'])) {
            $errors['national_id'][] = 'TC kimlik numarası 11 haneli ve 0 ile başlamamalıdır.';
        }

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

        if ($this->auth->nationalIdExists($data['national_id'])) {
            $_SESSION['errors'] = ['national_id' => ['Bu TC kimlik numarası ile kayıt mevcut.']];
            $old = $data;
            unset($old['password'], $old['password_confirmation']);
            $_SESSION['old'] = $old;
            Response::redirect('/register');
        }

        unset($_SESSION['errors'], $_SESSION['old']);

        try {
            $user = $this->auth->register($data);
        } catch (IdentityVerificationException $exception) {
            $_SESSION['errors'] = ['national_id' => [$exception->getMessage()]];
            $old = $data;
            unset($old['password'], $old['password_confirmation']);
            $_SESSION['old'] = $old;
            Response::redirect('/register');
        }
        $this->auth->loginUser($user);

        Response::redirect('/dashboard');
        return '';
    }

    public function showLogin(): string
    {
        $errors = flash_errors();
        $old = flash_old();

        return View::render('auth/login', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function login(): string
    {
        $data = Request::only(['email', 'password']);
        $data = array_map(static fn($value) => is_string($value) ? trim($value) : $value, $data);

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
