<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$user = Auth::user();
if (!$user) {
    header('Location: /login.php');
    exit;
}

$pdo = Database::connection();
$stmt = $pdo->prepare('SELECT id, income, credit_type, term, amount, ai_score, recommendations, created_at FROM credit_profiles WHERE user_id = :user_id ORDER BY created_at DESC');
$stmt->execute(['user_id' => $user['id']]);
$profiles = $stmt->fetchAll();
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - kredi.ltd</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/index.php" class="text-2xl font-semibold text-blue-600">kredi.ltd</a>
        <div class="space-x-4">
            <a href="/index.php" class="text-sm text-gray-600">Analiz</a>
            <button id="logoutButton" class="text-sm text-red-600">Çıkış Yap</button>
        </div>
    </div>
</nav>
<main class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-2xl font-semibold mb-6">Merhaba, <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES); ?></h1>
    <section class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Geçmiş Analizler</h2>
        <?php if (!$profiles): ?>
            <p class="text-sm text-gray-600">Henüz kayıtlı analiz bulunmuyor.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-4 py-2">Tarih</th>
                            <th class="text-left px-4 py-2">Gelir</th>
                            <th class="text-left px-4 py-2">Kredi Tipi</th>
                            <th class="text-left px-4 py-2">Tutar</th>
                            <th class="text-left px-4 py-2">Vade</th>
                            <th class="text-left px-4 py-2">AI Skor</th>
                            <th class="text-left px-4 py-2">Detay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profiles as $profile): $details = json_decode($profile['recommendations'], true) ?: []; ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo htmlspecialchars(date('d.m.Y H:i', strtotime($profile['created_at'] ?? 'now')), ENT_QUOTES); ?></td>
                                <td class="px-4 py-2">₺<?php echo number_format((float)$profile['income'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($profile['credit_type'], ENT_QUOTES); ?></td>
                                <td class="px-4 py-2">₺<?php echo number_format((float)$profile['amount'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-2"><?php echo (int)$profile['term']; ?> ay</td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars((string)($profile['ai_score'] ?? '-'), ENT_QUOTES); ?></td>
                                <td class="px-4 py-2">
                                    <?php if (!empty($details['recommended_banks'])): ?>
                                        <details>
                                            <summary class="cursor-pointer text-blue-600">Öneriler</summary>
                                            <ul class="list-disc list-inside text-gray-700">
                                                <?php foreach ($details['recommended_banks'] as $bank): ?>
                                                    <li><?php echo htmlspecialchars($bank, ENT_QUOTES); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php if (!empty($details['ai_comment'])): ?>
                                                <p class="text-sm mt-2"><?php echo htmlspecialchars($details['ai_comment'], ENT_QUOTES); ?></p>
                                            <?php endif; ?>
                                        </details>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<script>
    document.getElementById('logoutButton').addEventListener('click', async () => {
        await fetch('/api/logout.php');
        window.location.href = '/login.php';
    });
</script>
</body>
</html>
