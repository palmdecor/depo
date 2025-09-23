<?php

namespace App\Repositories;

use App\Models\ContractSubmission;
use App\Support\Database;
use PDO;

class ContractSubmissionRepository
{
    public function create(array $data): int
    {
        return ContractSubmission::create($data);
    }

    public function getByUser(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM contract_submissions WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
