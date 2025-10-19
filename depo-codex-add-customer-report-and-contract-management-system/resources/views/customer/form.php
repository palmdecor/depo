<?php $title = $title ?? 'Müşteri'; ?>
<?php ob_start(); ?>
<h1 class="h3 mb-4"><?= htmlspecialchars($title) ?></h1>

<?php include __DIR__ . '/../components/errors.php'; ?>

<form action="<?= htmlspecialchars($action) ?>" method="POST" class="row g-3">
    <?php if (!empty($customer['id'])): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($customer['id']) ?>">
    <?php endif; ?>

    <div class="col-md-6">
        <label for="first_name" class="form-label">Ad *</label>
        <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($customer['first_name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label">Soyad *</label>
        <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($customer['last_name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">E-posta *</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="phone" class="form-label">Telefon *</label>
        <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="domain_name" class="form-label">Domain *</label>
        <input type="text" id="domain_name" name="domain_name" class="form-control" value="<?= htmlspecialchars($customer['domain_name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="domain_expires_at" class="form-label">Domain Bitiş Tarihi *</label>
        <input type="date" id="domain_expires_at" name="domain_expires_at" class="form-control" value="<?= htmlspecialchars($customer['domain_expires_at'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label for="domain_renewal_period_months" class="form-label">Domain Yenileme Süresi (Ay)</label>
        <input type="number" min="1" step="1" id="domain_renewal_period_months" name="domain_renewal_period_months" class="form-control" value="<?= htmlspecialchars($customer['domain_renewal_period_months'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="domain_price" class="form-label">Domain Fiyatı</label>
        <div class="input-group">
            <span class="input-group-text">₺</span>
            <input type="number" min="0" step="0.01" id="domain_price" name="domain_price" class="form-control" value="<?= htmlspecialchars($customer['domain_price'] ?? '') ?>">
        </div>
    </div>
    <div class="col-md-6">
        <label for="hosting_service" class="form-label">Hosting Hizmeti</label>
        <input type="text" id="hosting_service" name="hosting_service" class="form-control" value="<?= htmlspecialchars($customer['hosting_service'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="hosting_expires_at" class="form-label">Hosting Bitiş Tarihi</label>
        <input type="date" id="hosting_expires_at" name="hosting_expires_at" class="form-control" value="<?= htmlspecialchars($customer['hosting_expires_at'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="hosting_renewal_period_months" class="form-label">Hosting Yenileme Süresi (Ay)</label>
        <input type="number" min="1" step="1" id="hosting_renewal_period_months" name="hosting_renewal_period_months" class="form-control" value="<?= htmlspecialchars($customer['hosting_renewal_period_months'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label for="hosting_price" class="form-label">Hosting Fiyatı</label>
        <div class="input-group">
            <span class="input-group-text">₺</span>
            <input type="number" min="0" step="0.01" id="hosting_price" name="hosting_price" class="form-control" value="<?= htmlspecialchars($customer['hosting_price'] ?? '') ?>">
        </div>
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Notlar</label>
        <textarea id="notes" name="notes" class="form-control" rows="4"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
    </div>
    <div class="col-12 d-flex justify-content-between">
        <a href="/customers" class="btn btn-outline-secondary">Listeye Dön</a>
        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($button ?? 'Kaydet') ?></button>
    </div>
</form>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
