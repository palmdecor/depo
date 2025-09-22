<?php
require_once __DIR__ . '/config.php';
require_admin();

$search = trim($_GET['q'] ?? '');
$sql = 'SELECT id, first_name, last_name, phone, email, is_active FROM users WHERE role = "customer"';
$params = [];
$types = '';

if ($search !== '') {
    $sql .= ' AND (CONCAT(first_name, " ", last_name) LIKE ? OR phone LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

$sql .= ' ORDER BY created_at DESC';
$stmt = $mysqli->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Müşteri Listesi';
$activePage = 'admin_dashboard';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Müşteri Yönetimi</h1>
    <p>Müşterileri ad, soyad veya telefon numarasına göre arayabilirsiniz.</p>
    <form method="get" style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <input type="search" name="q" placeholder="Ad, soyad veya telefon ara" value="<?php echo sanitize($search); ?>">
        <button type="submit">Ara</button>
        <a href="admin_dashboard.php" class="btn secondary">Filtreyi Temizle</a>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>E-posta</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$customers): ?>
                <tr>
                    <td colspan="5">Kayıtlı müşteri bulunamadı.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo sanitize($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                        <td><?php echo sanitize($customer['phone']); ?></td>
                        <td><?php echo sanitize($customer['email']); ?></td>
                        <td>
                            <span class="status-badge <?php echo ((int)$customer['is_active'] === 1) ? 'active' : 'blocked'; ?>">
                                <?php echo ((int)$customer['is_active'] === 1) ? 'Aktif' : 'Engelli'; ?>
                            </span>
                        </td>
                        <td>
                            <a class="btn secondary" href="admin_edit_customer.php?id=<?php echo (int)$customer['id']; ?>">Düzenle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
