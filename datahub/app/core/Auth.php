<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    public function user(): ?array
    {
        $session = Registry::get('session');
        $userId = $session->get('user_id');

        if (!$userId) {
            return null;
        }

        $userModel = new User();
        return $userModel->findById($userId);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            Registry::get('session')->set('user_id', $user['id']);
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        Registry::get('session')->remove('user_id');
    }
}
