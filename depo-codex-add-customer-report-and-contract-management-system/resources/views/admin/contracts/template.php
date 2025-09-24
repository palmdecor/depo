<?php $title = 'Sözleşme Şablonu'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Sözleşme Şablonu</h1>
<?php include __DIR__ . '/../../components/success.php'; ?>
<?php include __DIR__ . '/../../components/errors.php'; ?>
<div class="alert alert-info">
    <strong>Kullanılabilir alanlar:</strong>
    <ul class="mb-0">
        <li><code>{{first_name}}</code>, <code>{{last_name}}</code>, <code>{{full_name}}</code></li>
        <li><code>{{address}}</code>, <code>{{national_id}}</code>, <code>{{phone}}</code>, <code>{{email}}</code></li>
        <li><code>{{date}}</code> (PDF oluşturma tarihi)</li>
    </ul>
</div>
<form method="POST" action="/admin/contracts/template">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Şablon İçeriği</label>
        <textarea name="content" rows="15" class="form-control" required><?= htmlspecialchars($content ?? '') ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Kaydet</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layouts/app.php'; ?>
