<?php

namespace App\Repositories;

use App\Models\ContractSubmission;

class ContractSubmissionRepository
{
    public function create(array $data): int
    {
        return ContractSubmission::create($data);
    }
}
