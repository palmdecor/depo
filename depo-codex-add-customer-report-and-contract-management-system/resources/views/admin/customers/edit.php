<?php $title = 'Müşteri Düzenle'; ?>
<?php ob_start(); ?>
<a href="/admin/customers" class="btn btn-link ps-0">&larr; Listeye Dön</a>
<h1 class="mb-3">Müşteri: <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
<?php include __DIR__ . '/../../components/errors.php'; ?>
<?php include __DIR__ . '/../../components/success.php'; ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Hesap Bilgileri</h5>
                <ul class="list-unstyled mb-0">
                    <li><strong>E-posta:</strong> <?= htmlspecialchars($customer['email']) ?></li>
                    <li><strong>Telefon:</strong> <?= htmlspecialchars($customer['phone']) ?></li>
                    <li><strong>Kayıt Tarihi:</strong> <?= htmlspecialchars($customer['created_at'] ?? '') ?></li>
                    <li><strong>Son Giriş:</strong> <?= htmlspecialchars($customer['last_login_at'] ?? 'Belirtilmemiş') ?></li>
                    <li><strong>Durum:</strong>
                        <?php if ((int) $customer['is_blocked'] === 1): ?>
                            <span class="badge text-bg-danger">Engelli</span>
                        <?php else: ?>
                            <span class="badge text-bg-success">Aktif</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
            <div class="card-footer">
                <form method="POST" action="/admin/customers/status">
                    <?= \App\Support\Csrf::tokenField(); ?>
                    <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">
                    <?php if ((int) $customer['is_blocked'] === 1): ?>
                        <input type="hidden" name="action" value="unblock">
                        <button type="submit" class="btn btn-success w-100">Hesabı Aktifleştir</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="block">
                        <button type="submit" class="btn btn-danger w-100">Hesabı Engelle</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Rapor Yükle</h5>
                <form method="POST" action="/admin/customers/upload-report" enctype="multipart/form-data" class="row g-3 align-items-end">
                    <?= \App\Support\Csrf::tokenField(); ?>
                    <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">
                    <div class="col-md-8">
                        <label class="form-label">PDF Rapor Dosyası</label>
                        <input type="file" name="report" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Yükle</button>
                    </div>
                </form>
                <p class="text-muted small mt-2 mb-0">Sadece PDF dosyaları kabul edilir. Dosya adında Türkçe karakter kullanılabilir.</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Paylaşılan Raporlar</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Dosya Adı</th>
                            <th scope="col">Tarih</th>
                            <th scope="col">Boyut</th>
                            <th scope="col" class="text-end">İndir</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">Bu müşteri için henüz rapor yüklenmedi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['original_name']) ?></td>
                                    <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($report['uploaded_at'] ?? now()))) ?></td>
                                    <td><?= htmlspecialchars(number_format(($report['file_size'] ?? 0) / 1024, 2)) ?> KB</td>
                                    <td class="text-end">
                                        <a href="/customer/reports/download?id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-outline-primary">İndir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layouts/app.php'; ?>
