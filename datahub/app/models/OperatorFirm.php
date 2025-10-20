<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class OperatorFirm extends Model
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM operator_firms ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM operator_firms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO operator_firms (name, contact_email) VALUES (:name, :contact_email)');
        $stmt->execute([
            'name' => $data['name'],
            'contact_email' => $data['contact_email'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }
}
