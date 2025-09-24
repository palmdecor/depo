<?php $title = 'Müşteri Paneli'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Merhaba <?= htmlspecialchars($user['first_name'] ?? '') ?>!</h1>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Hesap Bilgileri</h5>
                <p class="mb-1"><strong>Ad Soyad:</strong> <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></p>
                <p class="mb-1"><strong>E-posta:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
                <p class="mb-1"><strong>Telefon:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
                <a class="btn btn-sm btn-outline-secondary mt-3" href="/password">Şifreyi Güncelle</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Raporlarım</h5>
                <p class="display-6 mb-1"><?= count($reports ?? []) ?></p>
                <p class="text-muted">Son yüklenen raporlarınız.</p>
                <a class="btn btn-sm btn-outline-primary" href="/reports">Tümünü Gör</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Sözleşmeler</h5>
                <p class="display-6 mb-1"><?= count($contracts ?? []) ?></p>
                <p class="text-muted">Son doldurulan sözleşmeleriniz.</p>
                <a class="btn btn-sm btn-outline-success" href="/contract">Yeni Sözleşme Doldur</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Son Raporlar</span>
                <a href="/reports" class="small">Tümü</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($reports)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($reports as $report): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($report['original_name']) ?></h6>
                                    <small class="text-muted">Yüklendi: <?= htmlspecialchars(date('d.m.Y H:i', strtotime($report['created_at']))) ?></small>
                                </div>
                                <a class="btn btn-sm btn-outline-primary" href="/reports/download?id=<?= (int) $report['id'] ?>">İndir</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="p-3 mb-0 text-muted">Henüz rapor yüklenmedi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Son Sözleşmeler</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($contracts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($contracts as $contract): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1"><?= htmlspecialchars($contract['first_name'] . ' ' . $contract['last_name']) ?></h6>
                                <small class="text-muted">Düzenleme: <?= htmlspecialchars(date('d.m.Y H:i', strtotime($contract['created_at']))) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="p-3 mb-0 text-muted">Henüz sözleşme doldurmadınız.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
