<?php $title = 'Müşteri Paneli'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Merhaba <?= htmlspecialchars($user['first_name'] ?? '') ?>!</h1>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Hesap Bilgileri</h5>
        <p class="card-text mb-1"><strong>Ad Soyad:</strong> <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></p>
        <p class="card-text mb-1"><strong>E-posta:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
        <p class="card-text mb-1"><strong>Telefon:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
        <p class="card-text"><strong>Rol:</strong> <?= htmlspecialchars($user['role'] ?? 'customer') ?></p>
    </div>
</div>
<p>Sistem geliştirme aşamasındadır. Yakında raporlarınızı ve sözleşmelerinizi buradan yönetebileceksiniz.</p>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
