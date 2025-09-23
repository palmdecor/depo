<?php

namespace App\Repositories;

use App\Models\User;
use App\Support\Database;

class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        return User::findBy(['email' => $email]);
    }

    public function find(int $id): ?array
    {
        return User::findBy(['id' => $id]);
    }

    public function create(array $data): int
    {
        return User::create($data);
    }

    public function update(int $id, array $data): void
    {
        User::update($id, $data);
    }

    public function customers(?string $search = null): array
    {
        $connection = Database::connection();
        $params = ['role' => 'customer'];
        $sql = 'SELECT * FROM users WHERE role = :role';

        if ($search !== null && $search !== '') {
            $sql .= ' AND (LOWER(first_name) LIKE :search OR LOWER(last_name) LIKE :search OR phone LIKE :search_phone)';
            $params['search'] = '%' . mb_strtolower($search, 'UTF-8') . '%';
            $params['search_phone'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public function countByRole(string $role): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS aggregate FROM users WHERE role = :role');
        $stmt->execute(['role' => $role]);
        $result = $stmt->fetch();
        return (int) ($result['aggregate'] ?? 0);
    }

    public function countBlockedCustomers(): int
    {
        $stmt = Database::connection()->query("SELECT COUNT(*) AS aggregate FROM users WHERE role = 'customer' AND is_blocked = 1");
        $result = $stmt->fetch();
        return (int) ($result['aggregate'] ?? 0);
    }

    public function setBlocked(int $userId, bool $blocked): void
    {
        $this->update($userId, [
            'is_blocked' => $blocked ? 1 : 0,
            'updated_at' => now(),
        ]);
    }
}
