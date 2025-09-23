<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use RuntimeException;

class ReportService
{
    public function __construct(private ReportRepository $reports)
    {
    }

    public function uploadForUser(int $userId, array $file, ?int $uploadedBy = null): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Dosya yüklenemedi.');
        }

        $originalName = $file['name'] ?? 'rapor.pdf';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            throw new RuntimeException('Sadece PDF dosyaları yüklenebilir.');
        }

        if (!class_exists('finfo')) {
            throw new RuntimeException('finfo uzantısı etkin değil. PDF doğrulaması yapılamıyor.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            throw new RuntimeException('Geçersiz dosya türü.');
        }

        $reportsDir = __DIR__ . '/../../storage/reports/' . $userId;
        if (!is_dir($reportsDir) && !mkdir($reportsDir, 0775, true) && !is_dir($reportsDir)) {
            throw new RuntimeException('Rapor klasörü oluşturulamadı.');
        }

        $fileName = 'report-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.pdf';
        $destination = $reportsDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Dosya taşınamadı.');
        }

        $size = filesize($destination) ?: 0;

        $reportId = $this->reports->create([
            'user_id' => $userId,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $destination,
            'file_size' => $size,
            'file_mime' => $mime,
            'uploaded_by' => $uploadedBy,
            'uploaded_at' => now(),
        ]);

        return $this->reports->find($reportId) ?? [];
    }
}
