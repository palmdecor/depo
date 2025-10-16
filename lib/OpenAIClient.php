<?php
// lib/OpenAIClient.php

declare(strict_types=1);

class OpenAIClient
{
    private string $apiKey;
    private string $model;

    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $this->apiKey = $apiKey ?: OPENAI_API_KEY;
        $this->model = $model ?: OPENAI_MODEL;
    }

    public function analyzeCredit(array $payload): array
    {
        $prompt = sprintf(
            'Kullanıcının aylık geliri: %s₺, kredi tipi: %s, tutar: %s₺, vade: %s ay. Bu verilere göre 0-100 arasında kredi uygunluk skoru, tahmini faiz oranı, önerilen bankalar ve kullanıcıya özel finansal tavsiye üret.',
            $payload['income'],
            $payload['credit_type'],
            $payload['amount'],
            $payload['term']
        );

        $response = $this->createChatCompletion([
            ['role' => 'system', 'content' => 'Finansal danışman olarak cevap ver.'],
            ['role' => 'user', 'content' => $prompt],
        ]);

        return $this->parseStructuredResponse($response);
    }

    public function chat(string $message): string
    {
        $response = $this->createChatCompletion([
            ['role' => 'system', 'content' => 'Kullanıcılara finansal tavsiyeler veren yardımcı bir asistansın.'],
            ['role' => 'user', 'content' => $message],
        ]);

        return $response['choices'][0]['message']['content'] ?? 'Şu anda yanıt veremiyorum.';
    }

    private function createChatCompletion(array $messages): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('OpenAI API anahtarı yapılandırılmadı.');
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $this->model,
                'messages' => $messages,
            ], JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('OpenAI isteği başarısız: ' . $error);
        }

        $statusCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        $decoded = json_decode($result, true);
        if ($statusCode >= 400) {
            throw new RuntimeException('OpenAI API hatası: ' . ($decoded['error']['message'] ?? 'Bilinmeyen hata'));
        }

        return $decoded;
    }

    private function parseStructuredResponse(array $response): array
    {
        $content = $response['choices'][0]['message']['content'] ?? '';

        $defaults = [
            'ai_score' => null,
            'recommended_banks' => [],
            'interest_rate_estimate' => null,
            'monthly_payment' => null,
            'ai_comment' => $content,
        ];

        if (empty($content)) {
            return $defaults;
        }

        $pattern = '/Skor:?\s*(\d+)/ui';
        if (preg_match($pattern, $content, $match)) {
            $defaults['ai_score'] = (int)$match[1];
        }

        $pattern = '/Oran(?:\s*:?\s*)([0-9]+(?:\.[0-9]+)?)/ui';
        if (preg_match($pattern, $content, $match)) {
            $defaults['interest_rate_estimate'] = (float)$match[1];
        }

        $pattern = '/Ayl[kı]k ödeme(?:\s*:?\s*)([0-9]+(?:\.[0-9]+)?)/ui';
        if (preg_match($pattern, $content, $match)) {
            $defaults['monthly_payment'] = (float)$match[1];
        }

        if (preg_match('/Bankalar:?\s*(.+)/ui', $content, $match)) {
            $banks = array_map('trim', explode(',', $match[1]));
            $defaults['recommended_banks'] = array_filter($banks);
        }

        return $defaults;
    }
}
