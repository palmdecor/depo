<?php $title = 'Raporlarım'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Raporlarım</h1>
<?php include __DIR__ . '/../components/errors.php'; ?>
<?php include __DIR__ . '/../components/success.php'; ?>
<?php if (empty($reports)): ?>
    <div class="alert alert-info">Henüz sizinle paylaşılmış bir rapor bulunmuyor.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th scope="col">Dosya Adı</th>
                <th scope="col">Yükleme Tarihi</th>
                <th scope="col">Boyut</th>
                <th scope="col" class="text-end">İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= htmlspecialchars($report['original_name']) ?></td>
                    <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($report['uploaded_at'] ?? now()))) ?></td>
                    <td><?= htmlspecialchars(number_format(($report['file_size'] ?? 0) / 1024, 2)) ?> KB</td>
                    <td class="text-end">
                        <a href="/customer/reports/download?id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-primary">
                            İndir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
