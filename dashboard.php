<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = '';

if (!isset($_SESSION['user']['profile'])) {
    $_SESSION['user']['profile'] = [
        'first_name' => 'Demo',
        'last_name' => 'Kullanıcı',
    ];
}

if (!isset($_SESSION['user']['password_hash'])) {
    $_SESSION['user']['password_hash'] = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($firstName === '') {
        $errors[] = 'Ad alanı boş bırakılamaz.';
    }

    if ($lastName === '') {
        $errors[] = 'Soyad alanı boş bırakılamaz.';
    }

    $newPassword = trim($newPassword);
    $confirmPassword = trim($confirmPassword);

    if ($newPassword !== '' || $confirmPassword !== '') {
        if ($newPassword === '' || $confirmPassword === '') {
            $errors[] = 'Yeni şifre ve tekrar alanları birlikte doldurulmalıdır.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Yeni şifre ile şifre tekrarı eşleşmiyor.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Yeni şifre en az 6 karakter olmalıdır.';
        }
    }

    if (!$errors) {
        $_SESSION['user']['profile']['first_name'] = $firstName;
        $_SESSION['user']['profile']['last_name'] = $lastName;

        if ($newPassword !== '') {
            $_SESSION['user']['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $success = 'Profil bilgileriniz güncellendi.';
    }
}

$user = $_SESSION['user'];
$profile = $user['profile'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="container dashboard">
        <aside class="sidebar">
            <h2>Kontrol Paneli</h2>
            <nav>
                <ul>
                    <li class="active"><span>Profil Ayarları</span></li>
                    <li class="disabled"><span>Güvenlik</span></li>
                    <li class="disabled"><span>Bildirimler</span></li>
                </ul>
            </nav>
            <form method="post" action="logout.php">
                <button type="submit" class="secondary">Çıkış Yap</button>
            </form>
        </aside>
        <main class="content">
            <h1>Hoş geldin, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p class="meta">Giriş zamanı: <?= date('d.m.Y H:i', $user['login_time']) ?></p>

            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section class="card">
                <h2>Profil Bilgileri</h2>
                <form method="post" action="">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-grid">
                        <div class="form-control">
                            <label for="first_name">Ad</label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="form-control">
                            <label for="last_name">Soyad</label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="form-control">
                        <label for="new_password">Yeni Şifre</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Yeni şifre belirleyin">
                    </div>
                    <div class="form-control">
                        <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Yeni şifreyi tekrar girin">
                    </div>

                    <button type="submit">Profili Güncelle</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
