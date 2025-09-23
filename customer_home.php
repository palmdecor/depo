<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    header('Location: admin_dashboard.php');
    exit;
}

$pageTitle = 'Anasayfa';
$activePage = 'home';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Hoş geldiniz, <?php echo sanitize($_SESSION['user']['first_name']); ?>!</h1>
    <p>Bu panel üzerinden raporlarınızı görüntüleyebilir ve şifrenizi güncelleyebilirsiniz.</p>
    <div style="margin-top: 2rem; display: grid; gap: 1rem;">
        <div>
            <h2>Hızlı İşlemler</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <a class="btn" href="customer_reports.php">Raporlarımı Görüntüle</a>
                <a class="btn secondary" href="customer_password.php">Şifremi Güncelle</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
