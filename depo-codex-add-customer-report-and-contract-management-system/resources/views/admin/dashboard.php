<?php $title = 'Yönetim Paneli'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Yönetim Paneli</h1>
<p class="text-muted">Sistemdeki müşteri hesaplarını, sözleşme şablonlarını ve rapor yüklemelerini buradan yönetebilirsiniz.</p>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Toplam Müşteri</h5>
                <p class="display-6 mb-0"><?= (int) ($customerCount ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Engelli Hesaplar</h5>
                <p class="display-6 mb-0"><?= (int) ($blockedCount ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Paylaşılan Raporlar</h5>
                <p class="display-6 mb-0"><?= (int) ($reportCount ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Müşteri Yönetimi</h5>
                <p class="card-text">Müşteri araması yapabilir, hesapları engelleyebilir veya rapor yükleyebilirsiniz.</p>
                <a href="/admin/customers" class="btn btn-primary">Müşteri Listesine Git</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Sözleşme Şablonu</h5>
                <p class="card-text">Sözleşme metnini güncelleyin, sistem genelinde kullanılacak dinamik alanları yönetin.</p>
                <a href="/admin/contract-template" class="btn btn-outline-primary">Şablonu Düzenle</a>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
