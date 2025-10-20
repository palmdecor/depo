<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class DataRecord extends Model
{
    public function createMany(array $records, int $memberId, float $price): void
    {
        $stmt = $this->db->prepare('INSERT INTO data_records (member_id, first_name, last_name, phone, category, status, price) VALUES (:member_id, :first_name, :last_name, :phone, :category, :status, :price)');

        foreach ($records as $record) {
            $stmt->execute([
                'member_id' => $memberId,
                'first_name' => $record['first_name'],
                'last_name' => $record['last_name'],
                'phone' => $record['phone'],
                'category' => $record['category'],
                'status' => 'pending',
                'price' => $price,
            ]);
        }
    }

    public function byStatus(string $status): array
    {
        $stmt = $this->db->prepare('SELECT dr.*, u.name as member_name FROM data_records dr JOIN users u ON dr.member_id = u.id WHERE dr.status = :status ORDER BY dr.created_at DESC');
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE data_records SET status = "approved" WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function reject(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE data_records SET status = "rejected" WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function getAvailable(): array
    {
        $stmt = $this->db->query('SELECT * FROM data_records WHERE status = "approved" AND sold_to IS NULL ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsSold(int $id, int $operatorId, float $price, float $commission): void
    {
        $stmt = $this->db->prepare('UPDATE data_records SET sold_to = :sold_to, status = "sold", sold_price = :price, commission = :commission, sold_at = NOW() WHERE id = :id');
        $stmt->execute([
            'sold_to' => $operatorId,
            'price' => $price,
            'commission' => $commission,
            'id' => $id,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM data_records WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function byMember(int $memberId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM data_records WHERE member_id = :member_id ORDER BY created_at DESC');
        $stmt->execute(['member_id' => $memberId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function purchasedByOperator(int $operatorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM data_records WHERE sold_to = :operatorId ORDER BY sold_at DESC');
        $stmt->execute(['operatorId' => $operatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
