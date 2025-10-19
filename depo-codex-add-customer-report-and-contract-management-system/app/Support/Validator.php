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

                if ($rule === 'email' && $value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Geçerli bir e-posta giriniz.';
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen((string) $value) < $min) {
                        $errors[$field][] = "En az {$min} karakter giriniz.";
                    }
                }

                if ($rule === 'date' && $value) {
                    if (strtotime((string) $value) === false) {
                        $errors[$field][] = 'Geçerli bir tarih giriniz.';
                    }
                }

                if (strpos($rule, 'date_format:') === 0 && $value) {
                    $format = substr($rule, strlen('date_format:'));
                    $date = \DateTime::createFromFormat($format, (string) $value);
                    $errorsList = \DateTime::getLastErrors();
                    $hasError = $date === false;

                    if ($date instanceof \DateTime) {
                        $hasError = $errorsList['warning_count'] > 0 || $errorsList['error_count'] > 0;
                    }

                    if ($hasError) {
                        $errors[$field][] = 'Tarih formatı geçersiz.';
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmation = $data[$field . '_confirmation'] ?? null;
                    if ($value !== $confirmation) {
                        $errors[$field][] = 'Onay alanı eşleşmiyor.';
                    }
                }

                if ($rule === 'integer' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field][] = 'Tam sayı bir değer giriniz.';
                }

                if ($rule === 'numeric' && $value !== null && $value !== '' && !is_numeric(str_replace(',', '.', (string) $value))) {
                    $errors[$field][] = 'Sayısal bir değer giriniz.';
                }

                if (strpos($rule, 'min_value:') === 0 && $value !== null && $value !== '') {
                    $minValue = (float) substr($rule, strlen('min_value:'));
                    $numericValue = is_numeric($value) ? (float) $value : (float) str_replace(',', '.', (string) $value);
                    if ($numericValue < $minValue) {
                        $errors[$field][] = "Değer en az {$minValue} olmalıdır.";
                    }
                }
            }
        }

        return $errors;
    }
}
