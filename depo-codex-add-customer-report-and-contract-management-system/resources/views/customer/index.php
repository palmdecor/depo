<?php $title = 'Müşteri Yönetimi'; ?>
<?php ob_start(); ?>
<?php $formatCurrency = static fn($value) => ($value === null || $value === '') ? null : number_format((float) $value, 2, ',', '.'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Müşteri Yönetimi</h1>
    <a href="/customers/create" class="btn btn-primary">Yeni Müşteri</a>
</div>

<?php if (empty($customers)): ?>
    <div class="alert alert-info">Henüz kayıtlı müşteri bulunmuyor.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Domain</th>
                    <th>Hosting</th>
                    <th>Notlar</th>
                    <th class="text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($customer['full_name'] ?: ($customer['first_name'] ?? '')) ?></strong><br>
                            <div class="text-muted small">E-posta: <?= htmlspecialchars($customer['email']) ?></div>
                            <div class="text-muted small">Telefon: <?= htmlspecialchars($customer['phone']) ?></div>
                        </td>
                        <td>
                            <div><strong><?= htmlspecialchars($customer['domain_name']) ?></strong></div>
                            <?php if (!empty($customer['domain_expires_at'])): ?>
                                <span class="badge bg-<?= htmlspecialchars($customer['domain_status']) ?>">
                                    <?= htmlspecialchars($customer['domain_expires_at']) ?>
                                </span>
                                <div class="small text-muted mt-1">
                                    <?php $domainDays = $customer['domain_days_remaining']; ?>
                                    <?php if ($domainDays === null): ?>
                                        Süre bilgisi yok
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
                                <div class="small text-muted mt-1">
                                    <?php $hostingDays = $customer['hosting_days_remaining']; ?>
                                    <?php if ($hostingDays === null): ?>
                                        Süre bilgisi yok
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
                        <td style="max-width: 220px;">
                            <div class="small text-muted"><?= nl2br(htmlspecialchars($customer['notes'] ?? '')) ?></div>
                        </td>
                        <td class="text-end">
                            <a href="/customers/edit?id=<?= urlencode((string) $customer['id']) ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                            <form action="/customers/delete" method="POST" class="d-inline" onsubmit="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($customer['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
