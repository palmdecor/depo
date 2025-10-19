<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{
    private CustomerRepository $customers;

    public function __construct(CustomerRepository $customers)
    {
        $this->customers = $customers;
    }

    public function listCustomers(): array
    {
        $customers = $this->customers->all();
        return array_map([$this, 'transformCustomer'], $customers);
    }

    public function find(int $id): ?array
    {
        $customer = $this->customers->find($id);

        return $customer ? $this->transformCustomer($customer) : null;
    }

    public function create(array $data): array
    {
        $payload = $this->prepareForStorage($data);
        $timestamp = date('Y-m-d H:i:s');
        $payload['created_at'] = $timestamp;
        $payload['updated_at'] = $timestamp;

        $id = $this->customers->create($payload);

        return $this->find($id);
    }

    public function update(int $id, array $data): ?array
    {
        $payload = $this->prepareForStorage($data);
        $payload['updated_at'] = date('Y-m-d H:i:s');

        $this->customers->update($id, $payload);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $this->customers->delete($id);
    }

    public function expiringWithin(int $days): array
    {
        $customers = $this->listCustomers();
        $result = [];

        foreach ($customers as $customer) {
            $domainDays = $customer['domain_days_remaining'];
            $hostingDays = $customer['hosting_days_remaining'];

            if (($domainDays !== null && $domainDays <= $days) || ($hostingDays !== null && $hostingDays <= $days)) {
                $result[] = $customer;
            }
        }

        return $result;
    }

    private function prepareForStorage(array $data): array
    {
        return [
            'first_name' => $this->normalizeString($data['first_name'] ?? ''),
            'last_name' => $this->normalizeString($data['last_name'] ?? ''),
            'email' => strtolower($this->normalizeString($data['email'] ?? '')),
            'phone' => $this->normalizeString($data['phone'] ?? ''),
            'domain_name' => $this->normalizeString($data['domain_name'] ?? ''),
            'domain_expires_at' => $this->normalizeDate($data['domain_expires_at'] ?? null),
            'domain_renewal_period_months' => $this->normalizeInt($data['domain_renewal_period_months'] ?? null),
            'domain_price' => $this->normalizeMoney($data['domain_price'] ?? null),
            'hosting_service' => $this->nullableString($data['hosting_service'] ?? null),
            'hosting_expires_at' => $this->normalizeDate($data['hosting_expires_at'] ?? null),
            'hosting_renewal_period_months' => $this->normalizeInt($data['hosting_renewal_period_months'] ?? null),
            'hosting_price' => $this->normalizeMoney($data['hosting_price'] ?? null),
            'notes' => $this->normalizeString($data['notes'] ?? '', true),
        ];
    }

    private function normalizeDate(?string $date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($dateTime instanceof \DateTimeImmutable) {
            return $dateTime->format('Y-m-d');
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }

    private function normalizeInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace([' ', ','], ['', ''], $value);
        }

        if (!is_numeric($value)) {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    private function normalizeMoney($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace([' ', ','], ['', '.'], $value);
        }

        if (!is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }

    private function normalizeString($value, bool $allowEmpty = false): string
    {
        $value = trim((string) $value);

        if ($allowEmpty) {
            return $value;
        }

        return $value;
    }

    private function nullableString($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function transformCustomer(array $customer): array
    {
        $customer['first_name'] = $this->normalizeString($customer['first_name'] ?? '', true);
        $customer['last_name'] = $this->normalizeString($customer['last_name'] ?? '', true);
        $customer['full_name'] = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
        $customer['email'] = strtolower($customer['email'] ?? '');
        $customer['phone'] = $this->normalizeString($customer['phone'] ?? '', true);
        $customer['domain_price'] = $this->normalizeMoney($customer['domain_price'] ?? null);
        $customer['hosting_price'] = $this->normalizeMoney($customer['hosting_price'] ?? null);
        $customer['domain_renewal_period_months'] = $this->normalizeInt($customer['domain_renewal_period_months'] ?? null);
        $customer['hosting_renewal_period_months'] = $this->normalizeInt($customer['hosting_renewal_period_months'] ?? null);
        $customer['domain_days_remaining'] = $this->daysUntil($customer['domain_expires_at'] ?? null);
        $customer['hosting_days_remaining'] = $this->daysUntil($customer['hosting_expires_at'] ?? null);
        $customer['domain_status'] = $this->statusFor($customer['domain_days_remaining']);
        $customer['hosting_status'] = $this->statusFor($customer['hosting_days_remaining']);

        return $customer;
    }

    private function daysUntil(?string $date): ?int
    {
        $dateTime = $this->parseDate($date);
        if (!$dateTime) {
            return null;
        }

        $today = new \DateTimeImmutable('today');
        $interval = $today->diff($dateTime);

        return (int) $interval->format('%r%a');
    }

    private function parseDate(?string $date): ?\DateTimeImmutable
    {
        if ($date === null || $date === '') {
            return null;
        }

        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($parsed instanceof \DateTimeImmutable) {
            return $parsed;
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return null;
        }

        return (new \DateTimeImmutable())->setTimestamp($timestamp);
    }

    private function statusFor(?int $days): string
    {
        if ($days === null) {
            return 'secondary';
        }

        if ($days < 0) {
            return 'danger';
        }

        if ($days <= 7) {
            return 'warning';
        }

        if ($days <= 30) {
            return 'info';
        }

        return 'success';
    }
}
