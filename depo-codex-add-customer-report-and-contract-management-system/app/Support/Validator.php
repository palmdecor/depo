<?php

namespace App\Support;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = 'Bu alan zorunludur.';
                }

                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Geçerli bir e-posta giriniz.';
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen((string) $value) < $min) {
                        $errors[$field][] = "En az {$min} karakter giriniz.";
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmation = $data[$field . '_confirmation'] ?? null;
                    if ($value !== $confirmation) {
                        $errors[$field][] = 'Onay alanı eşleşmiyor.';
                    }
                }

                if (str_starts_with($rule, 'digits:')) {
                    $digits = (int) substr($rule, 7);
                    if (!preg_match('/^\d{' . $digits . '}$/', (string) $value)) {
                        $errors[$field][] = $digits . ' haneli sayı girmelisiniz.';
                    }
                }
            }
        }

        return $errors;
    }
}
