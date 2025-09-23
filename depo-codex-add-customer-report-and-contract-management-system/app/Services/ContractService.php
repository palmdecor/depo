<?php

namespace App\Services;

use App\Repositories\ContractSubmissionRepository;
use App\Repositories\ContractTemplateRepository;
use Mpdf\Mpdf;
use RuntimeException;

class ContractService
{
    private string $tempDirectory;

    public function __construct(
        private ContractTemplateRepository $templates,
        private ContractSubmissionRepository $submissions
    ) {
        $this->tempDirectory = __DIR__ . '/../../storage/temp/mpdf';
        if (!is_dir($this->tempDirectory) && !mkdir($this->tempDirectory, 0775, true) && !is_dir($this->tempDirectory)) {
            throw new RuntimeException('PDF geçici klasörü oluşturulamadı.');
        }
    }

    public function getTemplate(): ?array
    {
        return $this->templates->getLatest();
    }

    public function saveTemplate(string $title, string $body): int
    {
        if (trim($title) === '' || trim($body) === '') {
            throw new RuntimeException('Başlık ve içerik zorunludur.');
        }

        return $this->templates->save([
            'title' => $title,
            'body' => $body,
        ]);
    }

    public function recordSubmission(int $userId, array $data): void
    {
        $this->submissions->create([
            'user_id' => $userId,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address' => $data['address'],
            'national_id' => $data['national_id'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'created_at' => now(),
        ]);
    }

    public function renderTemplate(string $body, array $payload): string
    {
        $firstName = htmlspecialchars($payload['first_name'], ENT_QUOTES, 'UTF-8');
        $lastName = htmlspecialchars($payload['last_name'], ENT_QUOTES, 'UTF-8');
        $fullName = trim($firstName . ' ' . $lastName);
        $nationalId = htmlspecialchars($payload['national_id'], ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars($payload['phone'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($payload['email'], ENT_QUOTES, 'UTF-8');

        $replacements = [
            '{{first_name}}' => $firstName,
            '{{last_name}}' => $lastName,
            '{{full_name}}' => $fullName,
            '{{address}}' => nl2br(htmlspecialchars($payload['address'], ENT_QUOTES, 'UTF-8')),
            '{{national_id}}' => $nationalId,
            '{{phone}}' => $phone,
            '{{email}}' => $email,
            '{{current_date}}' => date('d.m.Y'),
        ];

        $content = strtr($body, $replacements);
        return $content;
    }

    public function generatePdf(string $html, string $fileName): string
    {
        if (!class_exists(Mpdf::class)) {
            throw new RuntimeException('mPDF kütüphanesi yüklü değil. Lütfen `composer install` komutunu çalıştırın.');
        }

        $mpdf = new Mpdf([
            'tempDir' => $this->tempDirectory,
            'default_font' => 'DejaVuSans',
            'mode' => 'utf-8',
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'S');
    }
}
