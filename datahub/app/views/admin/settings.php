<section class="card">
    <h2>Genel Ayarlar</h2>
    <form method="post" action="<?= htmlspecialchars($view->url('admin/settings')) ?>">
        <label>Komisyon Oranı
            <input type="number" name="commission_rate" step="0.01" value="<?= htmlspecialchars($commission) ?>">
        </label>
        <button type="submit">Kaydet</button>
    </form>
</section>
