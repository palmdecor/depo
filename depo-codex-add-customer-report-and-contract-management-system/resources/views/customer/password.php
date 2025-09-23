<?php $title = 'Şifre Güncelle'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Şifre Güncelleme</h1>
<p class="text-muted">Yeni şifreniz en az 8 karakterden oluşmalı ve büyük/küçük harf ile rakam içermelidir.</p>
<?php include __DIR__ . '/../components/errors.php'; ?>
<?php include __DIR__ . '/../components/success.php'; ?>
<form method="POST" action="/customer/password" class="card">
    <div class="card-body">
        <?= \App\Support\Csrf::tokenField(); ?>
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
    </div>
    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">Şifremi Güncelle</button>
    </div>
</form>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
