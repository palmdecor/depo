<?php

namespace App\Repositories;

use App\Models\User;

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
}
