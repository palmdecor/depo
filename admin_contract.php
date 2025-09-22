<?php
require_once __DIR__ . '/config.php';
require_admin();

$pageTitle = 'Sözleşme Şablonu';
$activePage = 'admin_contract';

$contractContent = '';

$stmt = $mysqli->prepare('SELECT content FROM contract_templates WHERE id = 1');
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $contractContent = (string)$row['content'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    if ($content === '') {
        $_SESSION['flash'] = [
            'message' => 'Sözleşme içeriği boş olamaz.',
            'type' => 'error',
        ];
        $contractContent = $content;
    } else {
        $stmt = $mysqli->prepare('INSERT INTO contract_templates (id, content) VALUES (1, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)');
        $stmt->bind_param('s', $content);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash'] = [
            'message' => 'Sözleşme şablonu başarıyla güncellendi.',
            'type' => 'success',
        ];
        $contractContent = $content;
    }
}

include __DIR__ . '/partials/header.php';
?>
<div class="card">
    <h1>Sözleşme Şablonu</h1>
    <p>
        Müşteri sözleşmesi için temel metni aşağıdaki alandan düzenleyebilirsiniz. "[[MUSTERI_BILGILERI]]" etiketi,
        müşterinin doldurduğu bilgilerle otomatik olarak değiştirilecektir. Bu etiketi özellikle <strong>1. Taraflar</strong>
        bölümünde şirket bilgilerinin hemen altına yerleştirmeniz önerilir.
    </p>
    <?php display_flash(); ?>
    <form method="post" class="form-grid">
        <label for="contract-content">Sözleşme İçeriği</label>
        <textarea id="contract-content" name="content" rows="16" required><?php echo htmlspecialchars($contractContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>
        <button type="submit">Şablonu Kaydet</button>
    </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
