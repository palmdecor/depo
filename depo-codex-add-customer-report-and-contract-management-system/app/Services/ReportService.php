<?php

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(private ReportRepository $reports)
    {
    }

    public function uploadForUser(int $userId, array $file): array
    {
        if ($file['size'] <= 0) {
            throw new \InvalidArgumentException('Geçersiz dosya boyutu.');
        }

        $extension = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            throw new \InvalidArgumentException('Yalnızca PDF dosyaları yüklenebilir.');
        }

        $mime = $this->detectMimeType($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            throw new \InvalidArgumentException('Dosya tipi PDF olmalıdır.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \InvalidArgumentException('Dosya yüklenemedi.');
        }

        $directory = __DIR__ . '/../../public/uploads/reports/' . $userId;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $storedName = bin2hex(random_bytes(16)) . '.pdf';
        $fullPath = $directory . '/' . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \RuntimeException('Dosya kaydedilemedi.');
        }

        $relativePath = 'uploads/reports/' . $userId . '/' . $storedName;
        $now = date('Y-m-d H:i:s');

        $reportId = $this->reports->create([
            'user_id' => $userId,
            'original_name' => $file['name'],
            'stored_name' => $relativePath,
            'mime_type' => $mime,
            'size' => (int) $file['size'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->reports->find($reportId);
    }

    private function detectMimeType(string $path): string
    {
        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($path);
            if ($mime !== false) {
                return $mime;
            }
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $path) ?: 'application/octet-stream';
            finfo_close($finfo);
            return $mime;
        }

        return 'application/octet-stream';
    }
}
