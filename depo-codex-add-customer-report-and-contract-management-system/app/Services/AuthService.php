<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->users->findByEmail(strtolower($email));
    }

    public function register(array $data): array
    {
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $userId = $this->users->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => strtolower($data['email']),
            'password' => $passwordHash,
            'role' => $data['role'] ?? 'customer',
            'is_blocked' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->users->find($userId);
    }

    public function attempt(array $credentials): bool
    {
        $user = $this->users->findByEmail(strtolower($credentials['email']));

        if (!$user || (int) $user['is_blocked'] === 1) {
            return false;
        }

        if (!password_verify($credentials['password'], $user['password'])) {
            return false;
        }

        $this->loginUser($user);
        return true;
    }

    public function loginUser(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_login_at'] = date('Y-m-d H:i:s');
        $_SESSION['user_role'] = $user['role'] ?? 'customer';
        $_SESSION['user_name'] = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');

        $this->users->update((int) $user['id'], [
            'last_login_at' => $_SESSION['last_login_at'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return $this->users->find((int) $_SESSION['user_id']);
    }

    public function requireUser(): array
    {
        $user = $this->user();

        if (!$user) {
            \App\Http\Response::redirect('/login');
        }

        return $user;
    }

    public function isAdmin(?array $user = null): bool
    {
        $user = $user ?? $this->user();
        return ($user['role'] ?? null) === 'admin';
    }

    public function isCustomer(?array $user = null): bool
    {
        $user = $user ?? $this->user();
        return ($user['role'] ?? null) === 'customer';
    }

    public function updatePassword(int $userId, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->users->updatePassword($userId, $hash);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
