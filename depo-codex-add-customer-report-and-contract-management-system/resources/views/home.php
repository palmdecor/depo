<?php $title = $title ?? 'Depo'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="mb-3">Depo Müşteri Rapor ve Sözleşme Sistemi</h1>
    <p class="lead mb-4">Sözleşmelerinizi doldurun, raporlarınızı güvenle paylaşın.</p>
    <a class="btn btn-primary btn-lg me-2" href="/login">Giriş Yap</a>
    <a class="btn btn-outline-secondary btn-lg" href="/register">Kayıt Ol</a>
</div>
<div class="row g-4 pb-5">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Güvenli Üyelik</h5>
                <p class="card-text">Şifreleriniz BCRYPT ile saklanır, engellenen hesaplar giriş yapamaz. CSRF korumalı formlarla güvenle işlem yapın.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">PDF Rapor Yönetimi</h5>
                <p class="card-text">Yöneticiler PDF raporlar yükleyebilir, müşteriler raporlarını istedikleri zaman indirip görüntüleyebilir.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Dinamik Sözleşme Şablonları</h5>
                <p class="card-text">mPDF ile DejaVuSans fontu kullanılarak çok sayfalı sözleşme çıktıları alın, müşteri verileri şablonlara otomatik işlenir.</p>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layouts/app.php'; ?>
