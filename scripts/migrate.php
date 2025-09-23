<?php

require __DIR__ . '/../bootstrap.php';

use App\Support\Database;

$connection = Database::connection();
$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
$connection->exec($schema);

echo "Migrations completed." . PHP_EOL;
