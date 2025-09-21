<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$username = '';

// Demo user store with hashed password generated using password_hash('secret123', PASSWORD_DEFAULT)
$users = [
    'demo' => '$2y$12$JzgVL8USUfAkDIjgmsQybeActEQkHIkJKSM4Yp0l.l1ml4AT.4hhy',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Kullanıcı adı ve şifre boş bırakılamaz.';
    } elseif (!isset($users[$username]) || !password_verify($password, $users[$username])) {
        $errors[] = 'Kullanıcı adı veya şifre hatalı.';
    }

    if (!$errors) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'username' => $username,
            'login_time' => time(),
        ];
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Paneli</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Giriş Yap</h1>
        <?php if ($errors): ?>
            <div class="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" required>

            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Giriş</button>
        </form>
        <p class="hint">Demo kullanıcı bilgileri: demo / secret123</p>
    </div>
</body>
</html>
