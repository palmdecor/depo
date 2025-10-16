<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (Auth::user()) {
    header('Location: /dashboard.php');
    exit;
}

$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Oturum doğrulaması başarısız.';
    } else {
        $name = sanitize_string($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        if ($name === '' || !$email || strlen($password) < 6) {
            $error = 'Geçerli bir ad, e-posta ve en az 6 karakterli şifre giriniz.';
        } else {
            try {
                $user = Auth::register($name, $email, $password);
                $_SESSION['user'] = $user + ['token' => Auth::generateToken($user)];
                header('Location: /dashboard.php');
                exit;
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }
}
$csrf = csrf_token();
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - kredi.ltd</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow rounded-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-semibold text-center text-blue-600 mb-6">Kayıt Ol</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES); ?>">
            <div>
                <label class="block text-sm font-medium">Ad Soyad</label>
                <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">E-posta</label>
                <input type="email" name="email" class="mt-1 block w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Şifre</label>
                <input type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Kayıt Ol</button>
        </form>
        <p class="text-sm text-center text-gray-600 mt-4">Zaten hesabınız var mı? <a href="/login.php" class="text-blue-600">Giriş Yap</a></p>
        <p class="text-xs text-center text-gray-500 mt-6">© <?php echo date('Y'); ?> kredi.ltd</p>
    </div>
</div>
</body>
</html>
