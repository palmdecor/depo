<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Registry;
use App\Models\User;

class AuthController extends Controller
{
    public function index(): void
    {
        $this->redirect('auth/login');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->auth->attempt($email, $password)) {
                $user = $this->auth->user();
                if ($user['role'] === 'admin') {
                    $this->redirect('admin/dashboard');
                } elseif ($user['role'] === 'operator') {
                    $this->redirect('operator/dashboard');
                }
                $this->redirect('member/dashboard');
            }

            $error = 'Geçersiz e-posta veya şifre.';
            return $this->view->render('auth/login.php', compact('error'), 'layouts/auth.php');
        }

        $this->view->render('auth/login.php', [], 'layouts/auth.php');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$name || !$email || !$password) {
                $error = 'Tüm alanlar zorunludur.';
                return $this->view->render('auth/register.php', compact('error'), 'layouts/auth.php');
            }

            $userModel = new User();
            if ($userModel->findByEmail($email)) {
                $error = 'Bu e-posta ile kayıtlı bir kullanıcı zaten mevcut.';
                return $this->view->render('auth/register.php', compact('error'), 'layouts/auth.php');
            }

            $userId = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'member',
                'balance' => 0,
            ]);

            Registry::get('session')->set('user_id', $userId);
            $this->redirect('member/dashboard');
        }

        $this->view->render('auth/register.php', [], 'layouts/auth.php');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('auth/login');
    }
}
