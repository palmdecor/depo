<?php $title = 'Sözleşme Şablonu'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Sözleşme Şablonunu Düzenle</h1>
<p class="text-muted">Sözleşme metnini HTML formatında düzenleyebilirsiniz. Müşterilerin girdiği bilgiler aşağıdaki değişkenler ile yerleştirilir:</p>
<ul>
    <li><code>{{first_name}}</code> - Ad</li>
    <li><code>{{last_name}}</code> - Soyad</li>
    <li><code>{{full_name}}</code> - Ad + Soyad</li>
    <li><code>{{address}}</code> - Adres</li>
    <li><code>{{national_id}}</code> - TC Kimlik No</li>
    <li><code>{{phone}}</code> - Telefon</li>
    <li><code>{{email}}</code> - E-posta</li>
    <li><code>{{current_date}}</code> - Güncel tarih (gg.aa.yyyy)</li>
</ul>
<?php include __DIR__ . '/../components/errors.php'; ?>
<?php include __DIR__ . '/../components/success.php'; ?>
<form method="POST" action="/admin/contract-template" class="card">
    <div class="card-body">
        <?= \App\Support\Csrf::tokenField(); ?>
        <div class="mb-3">
            <label class="form-label">Şablon Başlığı</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($template['title'] ?? 'Standart Sözleşme') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Şablon İçeriği</label>
            <textarea name="body" class="form-control" rows="12" required><?= htmlspecialchars($template['body'] ?? '<h2>Sözleşme Başlığı</h2><p>{{full_name}} tarafından doldurulmuştur.</p>') ?></textarea>
        </div>
    </div>
    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">Şablonu Kaydet</button>
    </div>
</form>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
