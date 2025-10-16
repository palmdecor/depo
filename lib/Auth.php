<?php
// lib/Auth.php

declare(strict_types=1);

class Auth
{
    public static function register(string $name, string $email, string $password): array
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            throw new RuntimeException('Bu e-posta ile kayıtlı bir kullanıcı zaten var.');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
        ]);

        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $id = (int)($driver === 'pgsql' ? $pdo->lastInsertId('users_id_seq') : $pdo->lastInsertId());

        return [
            'id' => $id,
            'name' => $name,
            'email' => $email,
        ];
    }

    public static function attempt(string $email, string $password): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('Geçersiz kimlik bilgileri.');
        }

        return [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];
    }

    public static function generateToken(array $user): string
    {
        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'], JSON_UNESCAPED_UNICODE));
        $payload = self::base64UrlEncode(json_encode([
            'sub' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'exp' => time() + JWT_TTL,
        ], JSON_UNESCAPED_UNICODE));

        $signature = self::base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, JWT_SECRET, true));

        return $header . '.' . $payload . '.' . $signature;
    }

    public static function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;
        $expected = self::base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, JWT_SECRET, true));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = json_decode(self::base64UrlDecode($payload), true);
        if (($decoded['exp'] ?? 0) < time()) {
            return null;
        }

        return $decoded;
    }

    public static function user(): ?array
    {
        if (!empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            $decoded = self::validateToken($token);
            if ($decoded) {
                $user = [
                    'id' => (int)($decoded['sub'] ?? 0),
                    'name' => $decoded['name'] ?? '',
                    'email' => $decoded['email'] ?? '',
                    'token' => $token,
                ];
                $_SESSION['user'] = $user;
                return $user;
            }
        }

        return null;
    }

    public static function requireUser(): array
    {
        $user = self::user();
        if (!$user) {
            json_response(['error' => 'Yetkisiz erişim.'], 401);
        }

        return $user;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
