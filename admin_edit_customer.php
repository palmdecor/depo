<?php
require_once __DIR__ . '/config.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect_with_message('admin_dashboard.php', 'Geçersiz müşteri seçimi.', 'error');
}

$stmt = $mysqli->prepare('SELECT id, first_name, last_name, phone, email, is_active FROM users WHERE id = ? AND role = "customer" LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$customer) {
    redirect_with_message('admin_dashboard.php', 'Müşteri bulunamadı.', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $newStatus = ((int)$customer['is_active'] === 1) ? 0 : 1;
        $stmt = $mysqli->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->bind_param('ii', $newStatus, $customer['id']);
        if ($stmt->execute()) {
            $stmt->close();
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], $newStatus ? 'Müşteri hesabı aktifleştirildi.' : 'Müşteri hesabı engellendi.');
        }
        $stmt->close();
        redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Durum güncellenemedi.', 'error');
    }

    if ($action === 'upload_report') {
        if (!isset($_FILES['report']) || $_FILES['report']['error'] !== UPLOAD_ERR_OK) {
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Dosya yüklenirken hata oluştu.', 'error');
        }

        $file = $_FILES['report'];
        $allowedType = 'application/pdf';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mime !== $allowedType) {
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Sadece PDF dosyaları yükleyebilirsiniz.', 'error');
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Dosya boyutu 10MB üzerinde olamaz.', 'error');
        }

        $storedName = uniqid('report_', true) . '.pdf';
        $destination = __DIR__ . '/uploads/' . $storedName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Dosya kaydedilemedi.', 'error');
        }

        $stmt = $mysqli->prepare('INSERT INTO customer_reports (user_id, original_name, stored_name) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $customer['id'], $file['name'], $storedName);
        if ($stmt->execute()) {
            $stmt->close();
            redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'PDF raporu başarıyla yüklendi.');
        }
        $stmt->close();
        redirect_with_message('admin_edit_customer.php?id=' . $customer['id'], 'Rapor kaydedilirken hata oluştu.', 'error');
    }
}

$stmt = $mysqli->prepare('SELECT id, original_name, uploaded_at FROM customer_reports WHERE user_id = ? ORDER BY uploaded_at DESC');
$stmt->bind_param('i', $customer['id']);
$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Müşteri Düzenle';
$activePage = 'admin_dashboard';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Müşteri Düzenle</h1>
    <?php display_flash(); ?>
    <section style="display: grid; gap: 2rem;">
        <div>
            <h2>Müşteri Bilgileri</h2>
            <p><strong>Ad Soyad:</strong> <?php echo sanitize($customer['first_name'] . ' ' . $customer['last_name']); ?></p>
            <p><strong>Telefon:</strong> <a href="tel:<?php echo sanitize($customer['phone']); ?>"><?php echo sanitize($customer['phone']); ?></a></p>
            <p><strong>E-posta:</strong> <a href="mailto:<?php echo sanitize($customer['email']); ?>"><?php echo sanitize($customer['email']); ?></a></p>
            <p><strong>Durum:</strong> <span class="status-badge <?php echo ((int)$customer['is_active'] === 1) ? 'active' : 'blocked'; ?>"><?php echo ((int)$customer['is_active'] === 1) ? 'Aktif' : 'Engelli'; ?></span></p>
            <form method="post" style="margin-top: 1rem;">
                <input type="hidden" name="action" value="toggle_status">
                <button type="submit" class="btn <?php echo ((int)$customer['is_active'] === 1) ? 'danger' : ''; ?>">
                    <?php echo ((int)$customer['is_active'] === 1) ? 'Hesabı Engelle' : 'Hesabı Aktifleştir'; ?>
                </button>
            </form>
        </div>

        <div>
            <h2>PDF Raporu Yükle</h2>
            <form method="post" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
                <input type="hidden" name="action" value="upload_report">
                <input type="file" name="report" accept="application/pdf" required>
                <button type="submit">PDF Yükle</button>
            </form>
        </div>

        <div>
            <h2>Yüklenen Raporlar</h2>
            <?php if (!$reports): ?>
                <p>Bu müşteriye ait rapor bulunamadı.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Dosya Adı</th>
                            <th>Yüklenme Tarihi</th>
                            <th>İndir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo sanitize($report['original_name']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($report['uploaded_at'])); ?></td>
                                <td><a class="btn secondary" href="download_report.php?id=<?php echo (int)$report['id']; ?>">İndir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
