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

            if (is_string($value)) {
                $value = trim($value);
            }

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

                if (str_starts_with($rule, 'max:') && $value !== null) {
                    $max = (int) substr($rule, 4);
                    if (strlen((string) $value) > $max) {
                        $errors[$field][] = "En fazla {$max} karakter giriniz.";
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmation = $data[$field . '_confirmation'] ?? null;
                    if ($value !== $confirmation) {
                        $errors[$field][] = 'Onay alanı eşleşmiyor.';
                    }
                }

                if ($rule === 'numeric' && $value !== null && !is_numeric($value)) {
                    $errors[$field][] = 'Sadece rakam giriniz.';
                }

                if (str_starts_with($rule, 'digits:') && $value !== null) {
                    $digits = (int) substr($rule, 7);
                    if (!preg_match('/^\d{' . $digits . '}$/', (string) $value)) {
                        $errors[$field][] = "Tam olarak {$digits} haneli rakam giriniz.";
                    }
                }

                if ($rule === 'accepted') {
                    $acceptedValues = ['on', '1', 1, true, 'true', 'yes'];
                    if (!in_array($value, $acceptedValues, true)) {
                        $errors[$field][] = 'Bu alanın işaretlenmesi gerekiyor.';
                    }
                }
            }
        }

        return $errors;
    }
}
