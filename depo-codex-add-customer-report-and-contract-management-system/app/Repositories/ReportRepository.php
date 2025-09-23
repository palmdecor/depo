<?php

namespace App\Repositories;

use App\Models\Report;
use App\Support\Database;
use PDO;

class ReportRepository
{
    public function __construct()
    {
        // Ensure database initialized
        Database::connection();
    }

    public function create(array $data): int
    {
        return Report::create($data);
    }

    public function find(int $id): ?array
    {
        return Report::findBy(['id' => $id]);
    }

    public function forUser(int $userId): array
    {
        $connection = Database::connection();
        $stmt = $connection->prepare('SELECT * FROM reports WHERE user_id = :user_id ORDER BY uploaded_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function totalCount(): int
    {
        $stmt = Database::connection()->query('SELECT COUNT(*) AS aggregate FROM reports');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['aggregate'] ?? 0);
    }
}
