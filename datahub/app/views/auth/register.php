<section class="card">
    <h2>Kayıt Ol</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= htmlspecialchars($view->url('auth/register')) ?>">
        <label>Ad Soyad
            <input type="text" name="name" required>
        </label>
        <label>E-posta
            <input type="email" name="email" required>
        </label>
        <label>Şifre
            <input type="password" name="password" required>
        </label>
        <button type="submit">Kayıt Ol</button>
    </form>
    <p>Zaten hesabınız var mı? <a href="<?= htmlspecialchars($view->url('auth/login')) ?>">Giriş Yap</a></p>
</section>
