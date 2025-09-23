<?php

namespace App\Repositories;

use App\Models\ContractTemplate;
use App\Support\Database;
use PDO;

class ContractTemplateRepository
{
    public function __construct()
    {
        Database::connection();
    }

    public function getLatest(): ?array
    {
        $connection = Database::connection();
        $stmt = $connection->query('SELECT * FROM contract_templates ORDER BY updated_at DESC LIMIT 1');
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        return $template ?: null;
    }

    public function save(array $data): int
    {
        $existing = $this->getLatest();
        $timestamps = ['updated_at' => now()];
        if ($existing) {
            ContractTemplate::update((int) $existing['id'], array_merge($data, $timestamps));
            return (int) $existing['id'];
        }

        $data = array_merge($data, ['created_at' => now()], $timestamps);
        return ContractTemplate::create($data);
    }
}
