<?php

namespace App\Repositories;

use App\Models\User;
use App\Support\Database;
use PDO;

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

    public function findByNationalId(string $nationalId): ?array
    {
        return User::findBy(['national_id' => $nationalId]);
    }

    public function create(array $data): int
    {
        return User::create($data);
    }

    public function update(int $id, array $data): void
    {
        User::update($id, $data);
    }

    public function allCustomers(?string $query = null): array
    {
        $connection = Database::connection();
        $sql = 'SELECT * FROM users WHERE role = :role';
        $params = ['role' => 'customer'];

        if ($query) {
            $sql .= ' AND (LOWER(first_name) LIKE :q1 OR LOWER(last_name) LIKE :q2 OR phone LIKE :q3 OR LOWER(email) LIKE :q4)';
            $search = '%' . mb_strtolower($query) . '%';
            $params['q1'] = $search;
            $params['q2'] = $search;
            $params['q3'] = '%' . $query . '%';
            $params['q4'] = $search;
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $connection->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . ltrim($key, ':'), $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function toggleBlock(int $id, bool $blocked): void
    {
        User::update($id, [
            'is_blocked' => $blocked ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        User::update($id, [
            'password' => $passwordHash,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
