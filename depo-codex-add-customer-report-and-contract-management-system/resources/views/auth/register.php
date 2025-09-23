<?php $title = 'Kayıt Ol'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Kayıt Ol</h1>
<?php include __DIR__ . '/../components/errors.php'; ?>
<form method="POST" action="/register">
    <?= \App\Support\Csrf::tokenField(); ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Ad</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Soyad</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" class="form-control" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Telefon</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">E-posta</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Şifre</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Şifre (Tekrar)</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Kayıt Ol</button>
</form>
<p class="mt-3">Zaten hesabınız var mı? <a href="/login">Giriş yapın</a>.</p>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
