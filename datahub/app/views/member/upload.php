<section class="card">
    <h2>Excel ile Data Yükle</h2>
    <p>Şablon sütunları: Ad, Soyad, Telefon, Kategori</p>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($view->url('member/upload')) ?>">
        <input type="file" name="data_file" accept=".xlsx,.xls" required>
        <button type="submit">Yükle</button>
    </form>
</section>
