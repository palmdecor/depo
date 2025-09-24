<?php $title = 'Yönetici Paneli'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Yönetim Özeti</h1>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Toplam Müşteri</h5>
                <p class="display-5 mb-0"><?= (int) ($stats['total_customers'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Engelli Müşteriler</h5>
                <p class="display-5 mb-0"><?= (int) ($stats['blocked_customers'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Aktif Müşteriler</h5>
                <p class="display-5 mb-0"><?= (int) (($stats['total_customers'] ?? 0) - ($stats['blocked_customers'] ?? 0)) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Son Müşteriler</span>
        <a class="small" href="/admin/customers">Tümü</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($stats['recent_customers'])): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($stats['recent_customers'] as $customer): ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/admin/customers/edit?id=<?= (int) $customer['id'] ?>">
                        <div>
                            <strong><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></strong>
                            <div class="text-muted small"><?= htmlspecialchars($customer['email']) ?> · <?= htmlspecialchars($customer['phone']) ?></div>
                        </div>
                        <span class="badge <?= (int) $customer['is_blocked'] === 1 ? 'bg-danger' : 'bg-success' ?>"><?= (int) $customer['is_blocked'] === 1 ? 'Engelli' : 'Aktif' ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="p-3 mb-0 text-muted">Kayıtlı müşteri bulunmuyor.</p>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
