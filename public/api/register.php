<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

require_post();

$input = get_json_input();
$name = sanitize_string($input['name'] ?? '');
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $input['password'] ?? '';

if ($name === '' || !$email || strlen($password) < 6) {
    json_response(['error' => 'Geçerli bir ad, e-posta ve en az 6 karakterli şifre zorunludur.'], 422);
}

try {
    $user = Auth::register($name, $email, $password);
    $_SESSION['user'] = $user + ['token' => Auth::generateToken($user)];

    json_response([
        'user' => $user,
        'token' => $_SESSION['user']['token'],
    ], 201);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 400);
}
