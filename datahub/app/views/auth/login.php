<section class="card">
    <h2>Giriş Yap</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= htmlspecialchars($view->url('auth/login')) ?>">
        <label>E-posta
            <input type="email" name="email" required>
        </label>
        <label>Şifre
            <input type="password" name="password" required>
        </label>
        <button type="submit">Giriş Yap</button>
    </form>
    <p>Hesabınız yok mu? <a href="<?= htmlspecialchars($view->url('auth/register')) ?>">Kayıt Ol</a></p>
</section>
