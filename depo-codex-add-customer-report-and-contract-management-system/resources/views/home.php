<?php $title = $title ?? 'Depo'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="mb-3">Depo Müşteri Rapor ve Sözleşme Sistemi</h1>
    <p class="lead mb-4">Sözleşmelerinizi doldurun, raporlarınızı güvenle paylaşın.</p>
    <a class="btn btn-primary btn-lg me-2" href="/login">Giriş Yap</a>
    <a class="btn btn-outline-secondary btn-lg" href="/register">Kayıt Ol</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layouts/app.php'; ?>
