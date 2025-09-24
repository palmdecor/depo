<?php

namespace App\Services;

use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ContractTemplateRepository;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ContractService
{
    public function __construct(
        private ContractTemplateRepository $templates,
        private ContractSubmissionRepository $submissions
    ) {
    }

    public function getTemplate(): ?array
    {
        return $this->templates->latest();
    }

    public function saveTemplate(string $content): void
    {
        $this->templates->upsert($content);
    }

    public function createSubmission(array $user, array $data): array
    {
        $template = $this->getTemplate();

        if (!$template) {
            throw new \RuntimeException('Aktif sözleşme şablonu bulunamadı.');
        }

        if (!class_exists(Mpdf::class)) {
            throw new \RuntimeException('mPDF kütüphanesi yüklü değil. Lütfen "composer install" komutunu çalıştırın.');
        }

        $html = $this->fillTemplate($template['content'], $data);
        $file = $this->generatePdf((int) $user['id'], $html);

        $now = date('Y-m-d H:i:s');
        $this->submissions->create([
            'user_id' => (int) $user['id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address' => $data['address'],
            'national_id' => $data['national_id'],
            'phone' => $data['phone'],
            'email' => strtolower($data['email']),
            'pdf_path' => $file['relative_path'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $file;
    }

    private function fillTemplate(string $content, array $data): string
    {
        $replacements = [
            '{{first_name}}' => htmlspecialchars($data['first_name'], ENT_QUOTES, 'UTF-8'),
            '{{last_name}}' => htmlspecialchars($data['last_name'], ENT_QUOTES, 'UTF-8'),
            '{{full_name}}' => htmlspecialchars($data['first_name'] . ' ' . $data['last_name'], ENT_QUOTES, 'UTF-8'),
            '{{address}}' => nl2br(htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8')),
            '{{national_id}}' => htmlspecialchars($data['national_id'], ENT_QUOTES, 'UTF-8'),
            '{{phone}}' => htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'),
            '{{email}}' => htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8'),
            '{{date}}' => htmlspecialchars(date('d.m.Y'), ENT_QUOTES, 'UTF-8'),
        ];

        return strtr($content, $replacements);
    }

    private function generatePdf(int $userId, string $html): array
    {
        $storagePath = __DIR__ . '/../../public/uploads/contracts/' . $userId;
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $fileName = 'contract-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.pdf';
        $fullPath = $storagePath . '/' . $fileName;

        $tempDir = __DIR__ . '/../../storage/framework/mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new Mpdf([
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output($fullPath, Destination::FILE);

        return [
            'full_path' => $fullPath,
            'relative_path' => 'uploads/contracts/' . $userId . '/' . $fileName,
            'download_name' => 'sozlesme-' . date('Ymd-His') . '.pdf',
        ];
    }
}
