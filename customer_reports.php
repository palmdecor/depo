<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    header('Location: admin_dashboard.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT id, original_name, stored_name, uploaded_at FROM customer_reports WHERE user_id = ? ORDER BY uploaded_at DESC');
$stmt->bind_param('i', $_SESSION['user']['id']);
$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Raporlarım';
$activePage = 'reports';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Raporlarım</h1>
    <p>Yönetici tarafından yüklenen PDF raporlarını buradan indirebilirsiniz.</p>

    <?php if (!$reports): ?>
        <p>Henüz rapor yüklenmemiş.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Dosya Adı</th>
                    <th>Yüklenme Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo sanitize($report['original_name']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($report['uploaded_at'])); ?></td>
                        <td>
                            <a class="btn secondary" href="download_report.php?id=<?php echo (int)$report['id']; ?>">İndir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
