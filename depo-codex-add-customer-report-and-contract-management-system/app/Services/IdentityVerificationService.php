<?php

namespace App\Services;

use RuntimeException;

class IdentityVerificationService
{
    private const DEFAULT_WSDL = 'https://tckimlik.nvi.gov.tr/Service/KPSPublicV2.asmx?WSDL';

    private ?\SoapClient $client = null;

    public function __construct(private ?string $wsdl = null)
    {
    }

    public function verify(string $nationalId, string $firstName, string $lastName, int $birthYear): bool
    {
        if (!class_exists('SoapClient')) {
            throw new RuntimeException('Kimlik doğrulama servisi için SOAP uzantısı etkin değil.');
        }

        $normalizedId = preg_replace('/\D+/', '', $nationalId) ?? '';
        $normalizedFirstName = mb_strtoupper(trim($firstName), 'UTF-8');
        $normalizedLastName = mb_strtoupper(trim($lastName), 'UTF-8');
        $birthYear = (int) $birthYear;

        if ($normalizedId === '') {
            return false;
        }

        try {
            $client = $this->client ??= new \SoapClient($this->wsdl ?? self::DEFAULT_WSDL, [
                'trace' => false,
                'exceptions' => true,
            ]);

            $result = $client->TCKimlikNoDogrula([
                'TCKimlikNo' => $normalizedId,
                'Ad' => $normalizedFirstName,
                'Soyad' => $normalizedLastName,
                'DogumYili' => $birthYear,
            ]);

            return (bool) ($result->TCKimlikNoDogrulaResult ?? false);
        } catch (\Throwable $exception) {
            throw new RuntimeException('Kimlik doğrulama servisine ulaşılamadı.', 0, $exception);
        }
    }
}

