<?php
$maskPhone = function (string $phone): string {
    $digits = preg_replace('/\D+/', '', $phone);
    if (strlen($digits) < 10) {
        return $phone;
    }
    return substr($digits, 0, 2) . 'XX XXX ' . substr($digits, -4, 2) . ' ' . substr($digits, -2);
};
?>
<section class="card">
    <h2>Merhaba, <?= htmlspecialchars($user['name']) ?></h2>
    <p>Bakiyeniz: <strong><?= number_format((float)$user['balance'], 2) ?> ₺</strong></p>
</section>
<section class="card">
    <h3>Satın Alınabilir Data</h3>
    <table>
        <thead>
        <tr>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Kategori</th>
            <th>Fiyat</th>
            <th>İşlem</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($available as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['first_name']) ?></td>
                <td><?= htmlspecialchars($record['last_name']) ?></td>
                <td><?= htmlspecialchars($maskPhone($record['phone'])) ?></td>
                <td><?= htmlspecialchars($record['category']) ?></td>
                <td><?= number_format((float)$record['price'], 2) ?> ₺</td>
                <td>
                    <form method="post" action="<?= htmlspecialchars($view->url('operator/purchase')) ?>">
                        <input type="hidden" name="record_id" value="<?= (int)$record['id'] ?>">
                        <button type="submit">Satın Al</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($available)): ?>
        <p>Satın alınabilir data bulunmuyor.</p>
    <?php endif; ?>
</section>
<section class="card">
    <h3>Satın Alınan Data</h3>
    <table>
        <thead>
        <tr>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Kategori</th>
            <th>Satın Alma Tarihi</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($purchased as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['first_name']) ?></td>
                <td><?= htmlspecialchars($record['last_name']) ?></td>
                <td><?= htmlspecialchars($record['phone']) ?></td>
                <td><?= htmlspecialchars($record['category']) ?></td>
                <td><?= htmlspecialchars($record['sold_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($purchased)): ?>
        <p>Henüz satın aldığınız data bulunmuyor.</p>
    <?php endif; ?>
</section>
<section class="card">
    <h3>İşlem Geçmişi</h3>
    <table>
        <thead>
        <tr>
            <th>Tür</th>
            <th>Tutar</th>
            <th>Komisyon</th>
            <th>Açıklama</th>
            <th>Tarih</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= htmlspecialchars($transaction['type']) ?></td>
                <td><?= number_format((float)$transaction['amount'], 2) ?> ₺</td>
                <td><?= number_format((float)$transaction['commission'], 2) ?> ₺</td>
                <td><?= htmlspecialchars($transaction['description']) ?></td>
                <td><?= htmlspecialchars($transaction['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
