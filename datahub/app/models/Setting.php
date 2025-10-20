<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Setting extends Model
{
    public function get(string $key): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM settings WHERE `key` = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function set(string $key, $value): void
    {
        $stmt = $this->db->prepare('INSERT INTO settings (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = :value');
        $stmt->execute(['key' => $key, 'value' => $value]);
    }
}
