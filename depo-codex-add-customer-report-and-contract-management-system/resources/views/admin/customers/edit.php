<?php $title = 'Müşteri Düzenle'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Müşteri: <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
<?php include __DIR__ . '/../../components/success.php'; ?>
<?php include __DIR__ . '/../../components/errors.php'; ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Bilgiler</div>
            <div class="card-body">
                <p><strong>E-posta:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                <p><strong>Telefon:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                <p><strong>Durum:</strong> <span class="badge <?= (int) $customer['is_blocked'] === 1 ? 'bg-danger' : 'bg-success' ?>"><?= (int) $customer['is_blocked'] === 1 ? 'Engelli' : 'Aktif' ?></span></p>
                <p><strong>Son Giriş:</strong> <?= $customer['last_login_at'] ? htmlspecialchars($customer['last_login_at']) : '-' ?></p>
                <form method="POST" action="/admin/customers/status">
                    <?= csrf_field() ?>
                    <input type="hidden" name="user_id" value="<?= (int) $customer['id'] ?>">
                    <input type="hidden" name="action" value="<?= (int) $customer['is_blocked'] === 1 ? 'unblock' : 'block' ?>">
                    <button class="btn <?= (int) $customer['is_blocked'] === 1 ? 'btn-success' : 'btn-danger' ?> w-100" type="submit">
                        <?= (int) $customer['is_blocked'] === 1 ? 'Hesabı Aktifleştir' : 'Hesabı Engelle' ?>
                    </button>
                </form>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">PDF Rapor Yükle</div>
            <div class="card-body">
                <form method="POST" action="/admin/reports/upload" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="user_id" value="<?= (int) $customer['id'] ?>">
                    <div class="mb-3">
                        <input type="file" name="report" class="form-control" accept="application/pdf" required>
                        <small class="text-muted">Yalnızca PDF dosyaları kabul edilir.</small>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Rapor Yükle</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Yüklenen Raporlar</div>
            <div class="card-body p-0">
                <?php if (!empty($reports)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Dosya</th>
                                    <th>Yüklenme</th>
                                    <th>Boyut</th>
                                    <th class="text-end">İndir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($report['original_name']) ?></td>
                                        <td><?= htmlspecialchars($report['created_at']) ?></td>
                                        <td><?= htmlspecialchars(number_format((float) $report['size'] / 1024, 2)) ?> KB</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/reports/download?id=<?= (int) $report['id'] ?>">İndir</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="p-3 mb-0 text-muted">Bu müşteri için yüklenen rapor bulunmuyor.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Sözleşme Geçmişi</div>
            <div class="card-body p-0">
                <?php if (!empty($contracts)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>Tarih</th>
                                    <th class="text-end">PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contracts as $contract): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($contract['first_name'] . ' ' . $contract['last_name']) ?></td>
                                        <td><?= htmlspecialchars($contract['created_at']) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-secondary" href="/uploads/contracts/<?= (int) $customer['id'] ?>/<?= htmlspecialchars(basename($contract['pdf_path'])) ?>" target="_blank">Görüntüle</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="p-3 mb-0 text-muted">Bu müşteri henüz sözleşme doldurmadı.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layouts/app.php'; ?>
