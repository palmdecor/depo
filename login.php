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

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta giriniz.';
    }
    if ($password === '') {
        $errors[] = 'Şifre giriniz.';
    }

    if (!$errors) {
        $stmt = $mysqli->prepare('SELECT id, first_name, last_name, phone, email, password_salt, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && hash('sha512', $user['password_salt'] . $password) === $user['password_hash']) {
            if ((int)$user['is_active'] !== 1) {
                $errors[] = 'Hesabınız şu anda engellenmiştir. Lütfen yönetici ile iletişime geçiniz.';
            } else {
                $_SESSION['user'] = [
                    'id' => (int)$user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'role' => $user['role'],
                ];

                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: customer_home.php');
                }
                exit;
            }
        } else {
            $errors[] = 'E-posta veya şifre hatalı.';
        }
    }
}

$pageTitle = 'Giriş Yap';
$activePage = 'login';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Panele Giriş</h1>
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
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" value="<?php echo sanitize($email); ?>" required>
        </div>
        <div>
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Giriş Yap</button>
    </form>
    <p style="margin-top: 1rem;">Hesabınız yok mu? <a href="register.php">Kayıt olun</a>.</p>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
