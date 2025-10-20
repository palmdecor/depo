<section class="card">
    <h2>Hoş geldiniz, <?= htmlspecialchars($user['name']) ?></h2>
    <p>Bakiyeniz: <strong><?= number_format((float)$user['balance'], 2) ?> ₺</strong></p>
    <a class="button" href="<?= htmlspecialchars($view->url('member/upload')) ?>">Yeni Data Yükle</a>
</section>
<section class="card">
    <h3>Yüklenen Datalar</h3>
    <table>
        <thead>
        <tr>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Kategori</th>
            <th>Durum</th>
            <th>Fiyat</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['first_name']) ?></td>
                <td><?= htmlspecialchars($record['last_name']) ?></td>
                <td><?= htmlspecialchars($record['phone']) ?></td>
                <td><?= htmlspecialchars($record['category']) ?></td>
                <td><?= htmlspecialchars(strtoupper($record['status'])) ?></td>
                <td><?= number_format((float)$record['price'], 2) ?> ₺</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
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
