<?php $title = 'Şifre Güncelle'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Şifreyi Güncelle</h1>
<?php include __DIR__ . '/../components/success.php'; ?>
<?php include __DIR__ . '/../components/errors.php'; ?>
<form method="POST" action="/password">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Mevcut Şifre</label>
        <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Yeni Şifre</label>
        <input type="password" name="password" class="form-control" required minlength="8">
    </div>
    <div class="mb-3">
        <label class="form-label">Yeni Şifre (Tekrar)</label>
        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
    </div>
    <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
