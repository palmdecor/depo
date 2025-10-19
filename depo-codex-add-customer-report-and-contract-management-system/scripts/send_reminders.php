<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Repositories\CustomerRepository;
use App\Services\CustomerService;

$customerService = new CustomerService(new CustomerRepository());

$windowDays = (int) env('REMINDER_WINDOW_DAYS', 7);
if ($windowDays <= 0) {
    $windowDays = 7;
}

$customers = $customerService->expiringWithin($windowDays);

if (empty($customers)) {
    echo 'Gönderilecek hatırlatma bulunamadı.' . PHP_EOL;
    return;
}

$transport = strtolower((string) env('MAIL_TRANSPORT', 'log'));
$fromAddress = env('MAIL_FROM_ADDRESS', 'noreply@example.com');
$fromName = env('MAIL_FROM_NAME', 'Depo Hatırlatma Sistemi');
$fallbackEmail = env('REMINDER_FALLBACK_EMAIL', $fromAddress);
$logDirectory = __DIR__ . '/../storage/logs';
$logFile = $logDirectory . '/reminders.log';

if (!is_dir($logDirectory)) {
    mkdir($logDirectory, 0777, true);
}

foreach ($customers as $customer) {
    $recipient = $customer['email'] ?: $fallbackEmail;

    if (empty($recipient)) {
        echo 'E-posta adresi olmadığı için atlandı: ' . ($customer['full_name'] ?: $customer['domain_name']) . PHP_EOL;
        continue;
    }

    $subject = sprintf('Yenileme Uyarısı - %s', $customer['domain_name']);
    $bodyLines = [];
    $bodyLines[] = 'Merhaba ' . ($customer['full_name'] ?: '');
    $bodyLines[] = '';
    $bodyLines[] = sprintf('Aşağıdaki hizmetler %d gün içerisinde yenileme gerektirmektedir:', $windowDays);

    if (!empty($customer['domain_expires_at'])) {
        $line = sprintf('- Domain (%s): %s (%s)',
            $customer['domain_name'],
            $customer['domain_expires_at'],
            formatDays($customer['domain_days_remaining'])
        );
        $details = domainDetails($customer);
        $bodyLines[] = $line;
        foreach ($details as $detail) {
            $bodyLines[] = '  ' . $detail;
        }
    }

    if (!empty($customer['hosting_expires_at'])) {
        $line = sprintf('- Hosting%s: %s (%s)',
            $customer['hosting_service'] ? ' - ' . $customer['hosting_service'] : '',
            $customer['hosting_expires_at'],
            formatDays($customer['hosting_days_remaining'])
        );
        $details = hostingDetails($customer);
        $bodyLines[] = $line;
        foreach ($details as $detail) {
            $bodyLines[] = '  ' . $detail;
        }
    }

    $bodyLines[] = '';
    $bodyLines[] = 'Lütfen gerekli yenilemeleri gerçekleştiriniz.';
    $bodyLines[] = '';
    $bodyLines[] = 'İyi çalışmalar dileriz,';
    $bodyLines[] = $fromName;

    $body = implode(PHP_EOL, $bodyLines);

    if ($transport === 'mail' && function_exists('mail')) {
        $headers = [];
        if (!empty($fromAddress)) {
            $headers[] = 'From: ' . ($fromName ? sprintf('%s <%s>', $fromName, $fromAddress) : $fromAddress);
        }
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $success = mail($recipient, $subject, $body, implode("\r\n", $headers));

        if ($success) {
            echo 'E-posta gönderildi: ' . $recipient . PHP_EOL;
        } else {
            echo 'E-posta gönderilemedi, günlük kaydı yapılacak: ' . $recipient . PHP_EOL;
            file_put_contents($logFile, logEntry($recipient, $subject, $body), FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, logEntry($recipient, $subject, $body), FILE_APPEND);
        echo 'Loga yazıldı: ' . $recipient . PHP_EOL;
    }
}

function formatDays(?int $days): string
{
    if ($days === null) {
        return 'tarih bilgisi yok';
    }

    if ($days < 0) {
        return abs($days) . ' gün gecikti';
    }

    if ($days === 0) {
        return 'bugün doluyor';
    }

    return $days . ' gün kaldı';
}

function domainDetails(array $customer): array
{
    $details = [];
    if (!empty($customer['domain_renewal_period_months'])) {
        $details[] = 'Yenileme süresi: ' . $customer['domain_renewal_period_months'] . ' ay';
    }

    if ($customer['domain_price'] !== null && $customer['domain_price'] !== '') {
        $details[] = 'Fiyat: ₺' . formatMoney((float) $customer['domain_price']);
    }

    return $details;
}

function hostingDetails(array $customer): array
{
    $details = [];

    if (!empty($customer['hosting_renewal_period_months'])) {
        $details[] = 'Yenileme süresi: ' . $customer['hosting_renewal_period_months'] . ' ay';
    }

    if ($customer['hosting_price'] !== null && $customer['hosting_price'] !== '') {
        $details[] = 'Fiyat: ₺' . formatMoney((float) $customer['hosting_price']);
    }

    return $details;
}

function formatMoney(float $value): string
{
    return number_format($value, 2, ',', '.');
}

function logEntry(string $recipient, string $subject, string $body): string
{
    return sprintf(
        "[%s] %s\nKonu: %s\n%s\n\n",
        date('Y-m-d H:i:s'),
        $recipient,
        $subject,
        $body
    );
}
