<?php $title = 'Raporlarım'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Raporlarım</h1>
<?php include __DIR__ . '/../components/success.php'; ?>
<?php include __DIR__ . '/../components/errors.php'; ?>
<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($reports)): ?>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Dosya Adı</th>
                            <th>Yüklenme Tarihi</th>
                            <th>Boyut</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= htmlspecialchars($report['original_name']) ?></td>
                                <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($report['created_at']))) ?></td>
                                <td><?= htmlspecialchars(number_format((float) $report['size'] / 1024, 2)) ?> KB</td>
                                <td class="text-end">
                                    <a href="/reports/download?id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-outline-primary">İndir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="p-4 mb-0 text-muted">Henüz yüklenmiş rapor bulunmuyor.</p>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
