<?php $title = 'Müşteri Listesi'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Müşteri Listesi</h1>
<form class="row g-2 align-items-center mb-3" method="GET" action="/admin/customers">
    <div class="col-sm-8 col-lg-4">
        <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>" class="form-control" placeholder="Ad, soyad veya telefon">
    </div>
    <div class="col-sm-4 col-lg-2">
        <button type="submit" class="btn btn-primary w-100">Ara</button>
    </div>
</form>
<?php include __DIR__ . '/../../components/errors.php'; ?>
<?php include __DIR__ . '/../../components/success.php'; ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead>
                <tr>
                    <th scope="col">Ad Soyad</th>
                    <th scope="col">E-posta</th>
                    <th scope="col">Telefon</th>
                    <th scope="col">Durum</th>
                    <th scope="col" class="text-end">İşlemler</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">Kriterlerinize uygun müşteri bulunamadı.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= htmlspecialchars($customer['phone']) ?></td>
                            <td>
                                <?php if ((int) $customer['is_blocked'] === 1): ?>
                                    <span class="badge text-bg-danger">Engelli</span>
                                <?php else: ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="/admin/customers/edit?id=<?= (int) $customer['id'] ?>" class="btn btn-sm btn-outline-primary">Düzenle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layouts/app.php'; ?>
