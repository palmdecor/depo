<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    header('Location: admin_dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 8) {
        $errors[] = 'Yeni şifre en az 8 karakter olmalıdır.';
    }
    if ($new !== $confirm) {
        $errors[] = 'Yeni şifre ve tekrarı eşleşmiyor.';
    }

    if (!$errors) {
        $stmt = $mysqli->prepare('SELECT password_salt, password_hash FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $_SESSION['user']['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || hash('sha512', $user['password_salt'] . $current) !== $user['password_hash']) {
            $errors[] = 'Mevcut şifre yanlış.';
        } else {
            $salt = bin2hex(random_bytes(32));
            $hash = hash('sha512', $salt . $new);
            $stmt = $mysqli->prepare('UPDATE users SET password_salt = ?, password_hash = ? WHERE id = ?');
            $stmt->bind_param('ssi', $salt, $hash, $_SESSION['user']['id']);
            if ($stmt->execute()) {
                $stmt->close();
                redirect_with_message('customer_password.php', 'Şifreniz başarıyla güncellendi.');
            }
            $stmt->close();
            $errors[] = 'Şifre güncellenirken bir sorun oluştu.';
        }
    }
}

$pageTitle = 'Şifre Güncelle';
$activePage = 'password';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Şifrenizi Güncelleyin</h1>
    <?php display_flash(); ?>
    <?php if ($errors): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo sanitize($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" novalidate>
        <div>
            <label for="current_password">Mevcut Şifre</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div>
            <label for="new_password">Yeni Şifre</label>
            <input type="password" id="new_password" name="new_password" minlength="8" required>
        </div>
        <div>
            <label for="confirm_password">Yeni Şifre (Tekrar)</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
        </div>
        <button type="submit">Şifreyi Güncelle</button>
    </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
