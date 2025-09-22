<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Depo Yönetim';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><strong>Depo Panel</strong></div>
        <nav class="nav-links">
            <?php if (is_logged_in()): ?>
                <?php if (is_admin()): ?>
                    <a href="admin_dashboard.php" class="<?php echo ($activePage ?? '') === 'admin_dashboard' ? 'active' : ''; ?>">Müşteri Listesi</a>
                    <a href="logout.php">Çıkış Yap</a>
                <?php else: ?>
                    <a href="customer_home.php" class="<?php echo ($activePage ?? '') === 'home' ? 'active' : ''; ?>">Anasayfa</a>
                    <a href="customer_reports.php" class="<?php echo ($activePage ?? '') === 'reports' ? 'active' : ''; ?>">Raporlarım</a>
                    <a href="customer_password.php" class="<?php echo ($activePage ?? '') === 'password' ? 'active' : ''; ?>">Şifre Güncelle</a>
                    <a href="logout.php">Çıkış Yap</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php">Anasayfa</a>
                <a href="login.php" class="<?php echo ($activePage ?? '') === 'login' ? 'active' : ''; ?>">Giriş Yap</a>
                <a href="register.php" class="<?php echo ($activePage ?? '') === 'register' ? 'active' : ''; ?>">Kayıt Ol</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
