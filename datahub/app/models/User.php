<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, balance) VALUES (:name, :email, :password, :role, :balance)');
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'balance' => $data['balance'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBalance(int $id, float $amount): void
    {
        $stmt = $this->db->prepare('UPDATE users SET balance = :balance WHERE id = :id');
        $stmt->execute(['balance' => $amount, 'id' => $id]);
    }

    public function incrementBalance(int $id, float $amount): void
    {
        $stmt = $this->db->prepare('UPDATE users SET balance = balance + :amount WHERE id = :id');
        $stmt->execute(['amount' => $amount, 'id' => $id]);
    }

    public function decrementBalance(int $id, float $amount): void
    {
        $stmt = $this->db->prepare('UPDATE users SET balance = balance - :amount WHERE id = :id');
        $stmt->execute(['amount' => $amount, 'id' => $id]);
    }

    public function updateRole(int $id, string $role): void
    {
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->execute(['role' => $role, 'id' => $id]);
    }
}
