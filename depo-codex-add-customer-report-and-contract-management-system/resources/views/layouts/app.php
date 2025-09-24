<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Depo') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Depo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menüyü Aç">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <?php $role = $_SESSION['user_role'] ?? 'customer'; ?>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Yönetim Özeti</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/customers">Müşteri Listesi</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/contracts/template">Sözleşme Şablonu</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Anasayfa</a></li>
                        <li class="nav-item"><a class="nav-link" href="/reports">Raporlarım</a></li>
                        <li class="nav-item"><a class="nav-link" href="/contract">Sözleşme</a></li>
                        <li class="nav-item"><a class="nav-link" href="/password">Şifre Güncelle</a></li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <span class="navbar-text text-white-50 small d-none d-lg-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Çıkış</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login">Giriş</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Kayıt</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">
    <?= $content ?? '' ?>
</div>
</body>
</html>
