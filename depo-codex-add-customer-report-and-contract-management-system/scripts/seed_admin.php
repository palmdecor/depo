<?php

require __DIR__ . '/../bootstrap.php';

use App\Repositories\UserRepository;

$repo = new UserRepository();
$existing = $repo->findByEmail('admin@depo.local');

if ($existing) {
    echo "Admin already exists." . PHP_EOL;
    exit;
}

$repo->create([
    'first_name' => 'Sistem',
    'last_name' => 'Yöneticisi',
    'phone' => '+900000000000',
    'email' => 'admin@depo.local',
    'password' => password_hash('password123', PASSWORD_BCRYPT),
    'national_id' => '10000000000',
    'birth_year' => 1990,
    'role' => 'admin',
    'is_blocked' => 0,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

echo "Admin user created." . PHP_EOL;
