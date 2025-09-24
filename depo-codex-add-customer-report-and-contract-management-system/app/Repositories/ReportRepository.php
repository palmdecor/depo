<?php

namespace App\Repositories;

use App\Models\Report;
use App\Support\Database;
use PDO;

class ReportRepository
{
    public function create(array $data): int
    {
        return Report::create($data);
    }

    public function find(int $id): ?array
    {
        return Report::find($id);
    }

    public function getByUser(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM reports WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
