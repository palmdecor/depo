<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    if (is_admin()) {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: customer_home.php');
    }
    exit;
}

$pageTitle = 'Depo Paneline Hoş Geldiniz';
$activePage = 'home';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Depo Yönetim Paneli</h1>
    <p>Müşteri ve yönetici rolleri için modern, güvenli ve mobil uyumlu bir panel.</p>
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a class="btn" href="register.php">Hemen Kayıt Ol</a>
        <a class="btn secondary" href="login.php">Zaten hesabım var</a>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
