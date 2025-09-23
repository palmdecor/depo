<?php $title = 'Müşteri Yönetimi'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Müşteri Yönetimi</h1>
<?php include __DIR__ . '/../../components/success.php'; ?>
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" value="<?= htmlspecialchars($query ?? '') ?>" class="form-control" placeholder="Ad, soyad, telefon veya e-posta">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100" type="submit">Ara</button>
    </div>
    <div class="col-md-2">
        <a class="btn btn-outline-secondary w-100" href="/admin/customers">Sıfırla</a>
    </div>
</form>
<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($customers)): ?>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th>Durum</th>
                            <th>Son Giriş</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                <td>
                                    <span class="badge <?= (int) $customer['is_blocked'] === 1 ? 'bg-danger' : 'bg-success' ?>"><?= (int) $customer['is_blocked'] === 1 ? 'Engelli' : 'Aktif' ?></span>
                                </td>
                                <td><?= $customer['last_login_at'] ? htmlspecialchars($customer['last_login_at']) : '-' ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="/admin/customers/edit?id=<?= (int) $customer['id'] ?>">Görüntüle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="p-4 mb-0 text-muted">Kriterlerinize uygun müşteri bulunamadı.</p>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layouts/app.php'; ?>
