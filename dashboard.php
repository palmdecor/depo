<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Hoş geldin, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p>Sisteme başarıyla giriş yaptınız.</p>
        <p>Giriş zamanı: <?= date('d.m.Y H:i', $user['login_time']) ?></p>
        <form method="post" action="logout.php">
            <button type="submit" class="secondary">Çıkış Yap</button>
        </form>
    </div>
</body>
</html>
