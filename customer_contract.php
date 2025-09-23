<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    redirect_with_message('admin_dashboard.php', 'Sözleşme oluşturma ekranı yalnızca müşterilere açıktır.', 'error');
}

$userId = (int)($_SESSION['user']['id'] ?? 0);
$stmt = $mysqli->prepare('SELECT first_name, last_name, phone, email FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();
$stmt->close();

$customerName = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
$customerPhone = (string)($userData['phone'] ?? '');
$customerEmail = (string)($userData['email'] ?? '');

$values = [
    'customer_name' => $customerName,
    'customer_address' => '',
    'customer_id' => '',
    'customer_phone' => $customerPhone,
    'customer_email' => $customerEmail,
];
$errors = [];

$pageTitle = 'Sözleşmem';
$activePage = 'contract';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    if ($values['customer_name'] === '') {
        $errors[] = 'Ad Soyad alanı boş bırakılamaz.';
    }
    if ($values['customer_address'] === '') {
        $errors[] = 'Adres alanı boş bırakılamaz.';
    }
    if ($values['customer_id'] === '') {
        $errors[] = 'T.C. Kimlik No alanı boş bırakılamaz.';
    }
    if ($values['customer_phone'] === '') {
        $errors[] = 'Telefon alanı boş bırakılamaz.';
    }
    if ($values['customer_email'] === '') {
        $errors[] = 'E-posta alanı boş bırakılamaz.';
    }
    if (empty($_POST['confirm_read'])) {
        $errors[] = 'Sözleşmeyi onaylamadan devam edemezsiniz.';
    }

    $stmt = $mysqli->prepare('SELECT content FROM contract_templates WHERE id = 1');
    $stmt->execute();
    $templateResult = $stmt->get_result();
    $templateRow = $templateResult->fetch_assoc();
    $stmt->close();

    $contractTemplate = $templateRow['content'] ?? '';
    if ($contractTemplate === '') {
        $errors[] = 'Sözleşme şablonu bulunamadı. Lütfen yönetici ile iletişime geçin.';
    }

    if (!$errors) {
        $customerBlock = "Müşteri Bilgileri\n" .
            'Ad Soyad: ' . $values['customer_name'] . "\n" .
            'Adres: ' . $values['customer_address'] . "\n" .
            'T.C. Kimlik No: ' . $values['customer_id'] . "\n" .
            'Telefon: ' . $values['customer_phone'] . "\n" .
            'E-Posta: ' . $values['customer_email'];

        if (strpos($contractTemplate, '[[MUSTERI_BILGILERI]]') !== false) {
            $contractText = str_replace('[[MUSTERI_BILGILERI]]', $customerBlock, $contractTemplate);
        } else {
            $contractText = $customerBlock . "\n\n" . $contractTemplate;
        }

        require_once __DIR__ . '/lib/pdf_generator.php';

        $fileName = 'sozlesme_' . preg_replace('/[^0-9]/', '', $values['customer_phone']) . '.pdf';
        if ($fileName === 'sozlesme_.pdf') {
            $fileName = 'sozlesme.pdf';
        }

        output_contract_pdf($contractText, $fileName);
    }
}

include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Sözleşme Bilgileri</h1>
    <p>
        Lütfen sözleşme bilgilerinizi eksiksiz doldurun. Onay sonrasında sözleşme PDF olarak indirilecektir.
    </p>
    <?php if ($errors): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo sanitize($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" class="form-grid">
        <label for="customer_name">Ad Soyad</label>
        <input type="text" id="customer_name" name="customer_name" value="<?php echo sanitize($values['customer_name']); ?>" required>

        <label for="customer_address">Adres</label>
        <textarea id="customer_address" name="customer_address" rows="3" required><?php echo sanitize($values['customer_address']); ?></textarea>

        <label for="customer_id">T.C. Kimlik No</label>
        <input type="text" id="customer_id" name="customer_id" value="<?php echo sanitize($values['customer_id']); ?>" required>

        <label for="customer_phone">Telefon</label>
        <input type="text" id="customer_phone" name="customer_phone" value="<?php echo sanitize($values['customer_phone']); ?>" required>

        <label for="customer_email">E-Posta</label>
        <input type="email" id="customer_email" name="customer_email" value="<?php echo sanitize($values['customer_email']); ?>" required>

        <label class="checkbox-row">
            <input type="checkbox" name="confirm_read" value="1" <?php echo empty($_POST) ? '' : (empty($_POST['confirm_read']) ? '' : 'checked'); ?>>
            <span>Sözleşmeyi okudum ve onaylıyorum.</span>
        </label>

        <button type="submit">PDF Olarak İndir</button>
    </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
