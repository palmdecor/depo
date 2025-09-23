<?php $title = 'Sözleşme Formu'; ?>
<?php ob_start(); ?>
<h1 class="mb-4">Sözleşme Oluştur</h1>
<?php if (!$template): ?>
    <div class="alert alert-warning">Henüz sözleşme şablonu eklenmedi. Lütfen daha sonra tekrar deneyiniz.</div>
<?php else: ?>
    <p class="text-muted">Aşağıdaki bilgileri doldurarak sözleşme PDF dosyanızı oluşturabilirsiniz. Oluşturulan PDF otomatik olarak indirilecektir.</p>
    <?php include __DIR__ . '/../components/errors.php'; ?>
    <form method="POST" action="/customer/contract" class="card">
        <div class="card-body">
            <?= \App\Support\Csrf::tokenField(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ad</label>
                    <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($old['first_name'] ?? $user['first_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Soyad</label>
                    <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($old['last_name'] ?? $user['last_name'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Adres</label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="Mahalle, Cadde, No, İlçe / İl"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">TC Kimlik No</label>
                    <input type="text" name="national_id" class="form-control" minlength="11" maxlength="11" required value="<?= htmlspecialchars($old['national_id'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($old['phone'] ?? $user['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-posta</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($old['email'] ?? $user['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="1" id="agree" name="agree" required <?= !empty($old['agree']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="agree">
                    Sözleşme metnini okudum ve kabul ediyorum.
                </label>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Mevcut şablonda kullanılabilecek değişkenler: <code>{{first_name}}</code>, <code>{{last_name}}</code>, <code>{{full_name}}</code>, <code>{{address}}</code>, <code>{{national_id}}</code>, <code>{{phone}}</code>, <code>{{email}}</code>, <code>{{current_date}}</code>
            </div>
            <button type="submit" class="btn btn-primary">PDF İndir</button>
        </div>
    </form>
    <div class="card mt-4">
        <div class="card-header">Sözleşme Şablonu Önizlemesi</div>
        <div class="card-body">
            <?= $template ? $template['body'] : '' ?>
        </div>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/app.php'; ?>
