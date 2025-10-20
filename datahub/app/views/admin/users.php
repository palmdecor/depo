<section class="card">
    <h2>Kullanıcı Yönetimi</h2>
    <table>
        <thead>
        <tr>
            <th>Ad</th>
            <th>E-posta</th>
            <th>Rol</th>
            <th>Bakiye</th>
            <th>İşlemler</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= number_format((float)$user['balance'], 2) ?> ₺</td>
                <td class="actions">
                    <form method="post" class="inline" action="<?= htmlspecialchars($view->url('admin/users')) ?>">
                        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                        <input type="hidden" name="action" value="balance">
                        <input type="number" step="0.01" name="amount" placeholder="Tutar">
                        <button type="submit">Bakiye Yükle</button>
                    </form>
                    <form method="post" class="inline" action="<?= htmlspecialchars($view->url('admin/users')) ?>">
                        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                        <input type="hidden" name="action" value="role">
                        <select name="role">
                            <option value="member" <?= $user['role']==='member'?'selected':'' ?>>Üye</option>
                            <option value="operator" <?= $user['role']==='operator'?'selected':'' ?>>Operatör</option>
                            <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
                        </select>
                        <button type="submit">Rol Güncelle</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
