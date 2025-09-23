<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: customer_home.php');
    exit;
}

$errors = [];
$values = [
    'first_name' => '',
    'last_name' => '',
    'phone' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$values['first_name'] = trim($_POST['first_name'] ?? '');
$values['last_name'] = trim($_POST['last_name'] ?? '');
$values['phone'] = trim($_POST['phone'] ?? '');
$values['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($values['first_name'] === '') {
        $errors[] = 'Ad alanı zorunludur.';
    }
    if ($values['last_name'] === '') {
        $errors[] = 'Soyad alanı zorunludur.';
    }
    if ($values['phone'] === '') {
        $errors[] = 'Telefon numarası zorunludur.';
    }
    if (!preg_match('/^\+?[0-9\s-]{10,15}$/', $values['phone'])) {
        $errors[] = 'Telefon numarası 10-15 haneli olmalıdır.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi giriniz.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Şifre en az 8 karakter olmalıdır.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Şifre ve şifre tekrarı eşleşmiyor.';
    }

    if (!$errors) {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $values['email']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Bu e-posta ile daha önce kayıt olunmuş.';
        }
        $stmt->close();
    }

    if (!$errors) {
        $salt = bin2hex(random_bytes(32));
        $passwordHash = hash('sha512', $salt . $password);
        $role = 'customer';
        $isActive = 1;

        $stmt = $mysqli->prepare('INSERT INTO users (first_name, last_name, phone, email, password_salt, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssi', $values['first_name'], $values['last_name'], $values['phone'], $values['email'], $salt, $passwordHash, $role, $isActive);

        if ($stmt->execute()) {
            redirect_with_message('login.php', 'Kayıt başarıyla tamamlandı. Şimdi giriş yapabilirsiniz.');
        } else {
            $errors[] = 'Kayıt sırasında bir sorun oluştu. Lütfen tekrar deneyin.';
        }
        $stmt->close();
    }
}

$pageTitle = 'Kayıt Ol';
$activePage = 'register';
include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Yeni Müşteri Kaydı</h1>
    <p>Lütfen bilgilerinizi eksiksiz doldurun.</p>
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
            <label for="first_name">Ad</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo sanitize($values['first_name']); ?>" required>
        </div>
        <div>
            <label for="last_name">Soyad</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo sanitize($values['last_name']); ?>" required>
        </div>
        <div>
            <label for="phone">Telefon Numarası</label>
            <input type="tel" id="phone" name="phone" value="<?php echo sanitize($values['phone']); ?>" placeholder="05xxxxxxxxx" required>
        </div>
        <div>
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" value="<?php echo sanitize($values['email']); ?>" required>
        </div>
        <div>
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" minlength="8" required>
        </div>
        <div>
            <label for="confirm_password">Şifre Tekrarı</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
        </div>
        <button type="submit">Kaydı Tamamla</button>
    </form>
    <p style="margin-top: 1rem;">Zaten hesabınız var mı? <a href="login.php">Giriş yapın</a>.</p>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
