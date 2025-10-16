<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

require_post();

$input = get_json_input();
$message = sanitize_string($input['message'] ?? '');

if ($message === '') {
    json_response(['error' => 'Mesaj boş olamaz.'], 422);
}

Auth::requireUser();

try {
    $client = new OpenAIClient();
    $reply = $client->chat($message);
    json_response(['reply' => $reply]);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 500);
}
