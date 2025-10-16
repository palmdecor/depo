<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

require_get();

Auth::requireUser();

$offers = [
    ['bank' => 'Bank A', 'interest_rate' => 2.15, 'term' => 36, 'monthly_payment' => 4200.75],
    ['bank' => 'Bank B', 'interest_rate' => 1.98, 'term' => 24, 'monthly_payment' => 5200.10],
    ['bank' => 'Bank C', 'interest_rate' => 2.35, 'term' => 48, 'monthly_payment' => 3500.60],
];

json_response(['offers' => $offers]);
