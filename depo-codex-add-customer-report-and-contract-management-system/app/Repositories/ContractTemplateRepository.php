<?php

namespace App\Repositories;

use App\Models\ContractTemplate;
use App\Support\Database;
use PDO;

class ContractTemplateRepository
{
    public function latest(): ?array
    {
        $stmt = Database::connection()->query('SELECT * FROM contract_templates ORDER BY id DESC LIMIT 1');
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function upsert(string $content): void
    {
        $now = date('Y-m-d H:i:s');
        $existing = $this->latest();

        if ($existing) {
            ContractTemplate::update((int) $existing['id'], [
                'content' => $content,
                'updated_at' => $now,
            ]);
            return;
        }

        ContractTemplate::create([
            'content' => $content,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
