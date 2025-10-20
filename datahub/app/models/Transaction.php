<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Transaction extends Model
{
    public function log(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO transactions (buyer_id, seller_id, record_id, amount, commission, type, description) VALUES (:buyer_id, :seller_id, :record_id, :amount, :commission, :type, :description)');
        $stmt->execute([
            'buyer_id' => $data['buyer_id'] ?? null,
            'seller_id' => $data['seller_id'] ?? null,
            'record_id' => $data['record_id'] ?? null,
            'amount' => $data['amount'],
            'commission' => $data['commission'] ?? 0,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM transactions WHERE buyer_id = :id OR seller_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
