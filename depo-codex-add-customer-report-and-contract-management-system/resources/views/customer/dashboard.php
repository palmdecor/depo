<?php $title = 'Müşteri Paneli'; ?>
<?php ob_start(); ?>
<?php $formatCurrency = static fn($value) => ($value === null || $value === '') ? null : number_format((float) $value, 2, ',', '.'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Merhaba <?= htmlspecialchars($user['first_name'] ?? '') ?>!</h1>
    <a href="/customers" class="btn btn-outline-primary">Müşteri Yönetimine Git</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Hesap Bilgileri</h5>
                <p class="card-text mb-1"><strong>Ad Soyad:</strong> <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></p>
                <p class="card-text mb-1"><strong>E-posta:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
                <p class="card-text mb-1"><strong>Telefon:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
                <p class="card-text"><strong>Rol:</strong> <?= htmlspecialchars($user['role'] ?? 'customer') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Yenileme Hatırlatması</h5>
                <p class="card-text">Bu panel, domain ve hosting bitiş tarihlerini, yenileme periyotlarını ve fiyatları takip etmenize yardımcı olur.</p>
                <p class="card-text mb-0"><span class="badge bg-warning text-dark">Uyarı</span> Domain veya hosting süresi 7 gün içinde dolacak.</p>
                <p class="card-text mb-0"><span class="badge bg-danger">Kritik</span> Süresi dolmuş veya bugün dolacak kayıtlar.</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Önümüzdeki <?= (int) ($windowDays ?? 30) ?> Gün İçinde Yenilenecekler</h5>
            <a href="/customers" class="btn btn-sm btn-outline-secondary">Tüm Müşterileri Gör</a>
        </div>
        <?php if (empty($upcoming)): ?>
            <p class="text-muted mb-0">Yaklaşan domain veya hosting yenilemesi bulunmuyor.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Domain</th>
                            <th>Hosting</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $customer): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($customer['full_name'] ?: ($customer['first_name'] ?? '')) ?></strong><br>
                                    <span class="text-muted small">E-posta: <?= htmlspecialchars($customer['email']) ?> · Telefon: <?= htmlspecialchars($customer['phone']) ?></span>
                                </td>
                                <td>
                                    <div><strong><?= htmlspecialchars($customer['domain_name']) ?></strong></div>
                                    <?php if (!empty($customer['domain_expires_at'])): ?>
                                        <span class="badge bg-<?= htmlspecialchars($customer['domain_status']) ?>">
                                            <?= htmlspecialchars($customer['domain_expires_at']) ?>
                                        </span>
                                        <div class="small text-muted">
                                            <?php $domainDays = $customer['domain_days_remaining']; ?>
                                            <?php if ($domainDays === null): ?>
                                                Bilgi yok
                                            <?php elseif ($domainDays < 0): ?>
                                                <?= abs($domainDays) ?> gün gecikti
                                            <?php elseif ($domainDays === 0): ?>
                                                Bugün doluyor
                                            <?php else: ?>
                                                <?= $domainDays ?> gün kaldı
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($customer['domain_renewal_period_months'])): ?>
                                        <div class="small text-muted">Yenileme süresi: <?= htmlspecialchars($customer['domain_renewal_period_months']) ?> ay</div>
                                    <?php endif; ?>
                                    <?php $domainPrice = $formatCurrency($customer['domain_price'] ?? null); ?>
                                    <?php if ($domainPrice !== null): ?>
                                        <div class="small text-muted">Fiyat: ₺<?= htmlspecialchars($domainPrice) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($customer['hosting_service'])): ?>
                                        <div><strong><?= htmlspecialchars($customer['hosting_service']) ?></strong></div>
                                    <?php else: ?>
                                        <div><strong>Bilgi yok</strong></div>
                                    <?php endif; ?>
                                    <?php if (!empty($customer['hosting_expires_at'])): ?>
                                        <span class="badge bg-<?= htmlspecialchars($customer['hosting_status']) ?>">
                                            <?= htmlspecialchars($customer['hosting_expires_at']) ?>
                                        </span>
                                        <div class="small text-muted">
                                            <?php $hostingDays = $customer['hosting_days_remaining']; ?>
                                            <?php if ($hostingDays === null): ?>
                                                Bilgi yok
                                            <?php elseif ($hostingDays < 0): ?>
                                                <?= abs($hostingDays) ?> gün gecikti
                                            <?php elseif ($hostingDays === 0): ?>
                                                Bugün doluyor
                                            <?php else: ?>
                                                <?= $hostingDays ?> gün kaldı
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($customer['hosting_renewal_period_months'])): ?>
                                        <div class="small text-muted">Yenileme süresi: <?= htmlspecialchars($customer['hosting_renewal_period_months']) ?> ay</div>
                                    <?php endif; ?>
                                    <?php $hostingPrice = $formatCurrency($customer['hosting_price'] ?? null); ?>
                                    <?php if ($hostingPrice !== null): ?>
                                        <div class="small text-muted">Fiyat: ₺<?= htmlspecialchars($hostingPrice) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
