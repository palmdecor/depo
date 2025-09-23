<?php $title = 'Müşteri Paneli'; ?>
<?php ob_start(); ?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h1 class="mb-0">Merhaba <?= htmlspecialchars($user['first_name'] ?? '') ?>!</h1>
        <p class="text-muted">Depo platformuna hoş geldiniz. Aşağıdaki menüleri kullanarak işlemlerinizi gerçekleştirebilirsiniz.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <span class="badge bg-secondary">Müşteri</span>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Raporlarım</h5>
                <p class="card-text">Yönetici tarafından paylaşılan raporları görüntüleyin ve indirin.</p>
                <a href="/customer/reports" class="btn btn-primary">Raporlara Git</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Sözleşme Oluştur</h5>
                <p class="card-text">Bilgilerinizi doldurarak güncel sözleşme şablonunu PDF olarak indirin.</p>
                <a href="/customer/contract" class="btn btn-outline-primary">Sözleşme Formu</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Şifre Güncelle</h5>
                <p class="card-text">Hesabınızın güvenliği için şifrenizi düzenli aralıklarla güncelleyin.</p>
                <a href="/customer/password" class="btn btn-outline-secondary">Şifremi Güncelle</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Hesap Bilgileri</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1"><strong>Ad Soyad:</strong> <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></p>
                <p class="mb-1"><strong>E-posta:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
                <p class="mb-1"><strong>Telefon:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>Son Giriş:</strong> <?= htmlspecialchars($user['last_login_at'] ?? 'Henüz giriş yapılmadı') ?></p>
                <p class="mb-0"><strong>Hesap Oluşturma:</strong> <?= htmlspecialchars($user['created_at'] ?? '') ?></p>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
