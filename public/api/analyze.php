<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

require_post();

$input = get_json_input();

$income = (float)($input['income'] ?? 0);
$creditType = sanitize_string($input['credit_type'] ?? '');
$term = (int)($input['term'] ?? 0);
$amount = (float)($input['amount'] ?? 0);

if ($income <= 0 || $term <= 0 || $amount <= 0 || $creditType === '') {
    json_response(['error' => 'Tüm alanlar zorunludur.'], 422);
}

$user = Auth::requireUser();

try {
    $client = new OpenAIClient();
    $analysis = $client->analyzeCredit([
        'income' => $income,
        'credit_type' => $creditType,
        'term' => $term,
        'amount' => $amount,
    ]);

    $pdo = Database::connection();
    $stmt = $pdo->prepare('INSERT INTO credit_profiles (user_id, income, credit_type, term, amount, ai_score, recommendations) VALUES (:user_id, :income, :credit_type, :term, :amount, :ai_score, :recommendations)');
    $stmt->execute([
        'user_id' => $user['id'],
        'income' => $income,
        'credit_type' => $creditType,
        'term' => $term,
        'amount' => $amount,
        'ai_score' => $analysis['ai_score'],
        'recommendations' => json_encode($analysis, JSON_UNESCAPED_UNICODE),
    ]);

    json_response($analysis);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 500);
}
