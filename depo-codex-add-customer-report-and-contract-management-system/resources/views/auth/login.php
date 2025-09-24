<?php $title = 'Giriş Yap'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Giriş Yap</h1>
<?php include __DIR__ . '/../components/errors.php'; ?>
<form method="POST" action="/login">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">E-posta</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Şifre</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Giriş Yap</button>
</form>
<p class="mt-3">Hesabınız yok mu? <a href="/register">Kayıt olun</a>.</p>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
