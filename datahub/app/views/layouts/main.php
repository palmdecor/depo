<?php /** @var callable $content */ ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataHub</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($view->asset('assets/css/style.css')) ?>">
</head>
<body>
<header class="navbar">
    <div class="container">
        <h1><a href="<?= htmlspecialchars($view->url()) ?>">DataHub</a></h1>
        <nav>
            <a href="<?= htmlspecialchars($view->url('member/dashboard')) ?>">Üye Paneli</a>
            <a href="<?= htmlspecialchars($view->url('operator/dashboard')) ?>">Operatör Paneli</a>
            <a href="<?= htmlspecialchars($view->url('admin/dashboard')) ?>">Admin Paneli</a>
            <a href="<?= htmlspecialchars($view->url('auth/logout')) ?>">Çıkış</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php $content(); ?>
</main>
<footer class="footer">
    <div class="container">&copy; <?= date('Y') ?> DataHub</div>
</footer>
</body>
</html>
