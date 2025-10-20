<section class="card">
    <h2>Bekleyen Data</h2>
    <table>
        <thead>
        <tr>
            <th>Üye</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Kategori</th>
            <th>İşlemler</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pending as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['member_name']) ?></td>
                <td><?= htmlspecialchars($record['first_name']) ?></td>
                <td><?= htmlspecialchars($record['last_name']) ?></td>
                <td><?= htmlspecialchars($record['phone']) ?></td>
                <td><?= htmlspecialchars($record['category']) ?></td>
                <td class="actions">
                    <form method="post" class="inline" action="<?= htmlspecialchars($view->url('admin/data')) ?>">
                        <input type="hidden" name="record_id" value="<?= (int)$record['id'] ?>">
                        <button type="submit" name="action" value="approve">Onayla</button>
                        <button type="submit" name="action" value="reject" class="danger">Reddet</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<section class="card">
    <h2>Onaylı Data</h2>
    <table>
        <thead>
        <tr>
            <th>Üye</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Kategori</th>
            <th>Fiyat</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($approved as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['member_name']) ?></td>
                <td><?= htmlspecialchars($record['first_name']) ?></td>
                <td><?= htmlspecialchars($record['last_name']) ?></td>
                <td><?= htmlspecialchars($record['phone']) ?></td>
                <td><?= htmlspecialchars($record['category']) ?></td>
                <td><?= number_format((float)$record['price'], 2) ?> ₺</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
