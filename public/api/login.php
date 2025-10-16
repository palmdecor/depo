<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

require_post();

$input = get_json_input();
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $input['password'] ?? '';

if (!$email || $password === '') {
    json_response(['error' => 'E-posta ve şifre zorunludur.'], 422);
}

try {
    $user = Auth::attempt($email, $password);
    $_SESSION['user'] = $user + ['token' => Auth::generateToken($user)];

    json_response([
        'user' => $user,
        'token' => $_SESSION['user']['token'],
    ]);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 401);
}
