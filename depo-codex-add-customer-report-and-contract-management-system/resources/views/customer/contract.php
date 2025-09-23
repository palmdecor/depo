<?php $title = 'Sözleşme Doldur'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Sözleşme Doldur</h1>
<?php if (!$template): ?>
    <div class="alert alert-warning">Şu anda aktif bir sözleşme şablonu bulunmuyor. Lütfen daha sonra tekrar deneyin.</div>
<?php else: ?>
    <?php include __DIR__ . '/../components/errors.php'; ?>
    <form method="POST" action="/contract">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ad</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? $user['first_name'] ?? '') ?>" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Soyad</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? $user['last_name'] ?? '') ?>" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Adres</label>
            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">TC Kimlik No</label>
                <input type="text" name="national_id" value="<?= htmlspecialchars($old['national_id'] ?? '') ?>" class="form-control" minlength="11" maxlength="11" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Telefon</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($old['phone'] ?? $user['phone'] ?? '') ?>" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? $user['email'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="agree" value="1" id="contractAgree" <?= !empty($old['agree']) ? 'checked' : '' ?> required>
            <label class="form-check-label" for="contractAgree">Sözleşme şartlarını okudum, onaylıyorum.</label>
        </div>
        <button type="submit" class="btn btn-success">PDF Oluştur ve İndir</button>
    </form>
    <div class="mt-4">
        <h5>Sözleşme Şablonu Önizlemesi</h5>
        <div class="border rounded p-3 bg-light" style="max-height: 400px; overflow:auto;">
            <?= $template ? $template['content'] : '' ?>
        </div>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
