<?php

namespace App\Controllers\Customer;

use App\Controllers\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\Csrf;
use App\Support\Validator;
use App\Support\View;

class PasswordController extends Controller
{
    public function __construct(AuthService $auth, private UserRepository $users)
    {
        parent::__construct($auth);
    }

    public function show(): string
    {
        $user = $this->requireUser();
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['errors'], $_SESSION['success']);

        return View::render('customer/password', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            exit('Geçersiz oturum doğrulaması.');
        }

        $user = $this->requireUser();
        $data = Request::only(['current_password', 'password', 'password_confirmation']);

        $errors = Validator::validate($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            Response::redirect('/customer/password');
        }

        if (!password_verify($data['current_password'], $user['password'])) {
            $_SESSION['errors'] = ['current_password' => ['Mevcut şifre hatalı.']];
            Response::redirect('/customer/password');
        }

        $this->users->update((int) $user['id'], [
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'updated_at' => now(),
        ]);

        $_SESSION['success'] = 'Şifreniz başarıyla güncellendi.';
        Response::redirect('/customer/password');
    }
}
