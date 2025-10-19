<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function all(): array
    {
        return Customer::all('last_name');
    }

    public function find(int $id): ?array
    {
        return Customer::findBy(['id' => $id]);
    }

    public function create(array $data): int
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): void
    {
        Customer::update($id, $data);
    }

    public function delete(int $id): void
    {
        Customer::delete($id);
    }
}
