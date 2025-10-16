<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$user = Auth::user();
$csrf = csrf_token();
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kredi.ltd AI Kredi Asistanı</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">
<nav class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/index.php" class="text-2xl font-semibold text-blue-600">kredi.ltd</a>
        <div class="space-x-4">
            <?php if ($user): ?>
                <a href="/dashboard.php" class="text-sm text-gray-600 hover:text-gray-900">Dashboard</a>
                <button id="logoutButton" class="text-sm text-red-600">Çıkış Yap</button>
            <?php else: ?>
                <a href="/login.php" class="text-sm text-blue-600">Giriş</a>
                <a href="/register.php" class="text-sm text-blue-600">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-8">
        <section class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Kredi Analizi</h2>
            <?php if (!$user): ?>
                <p class="text-sm text-red-600 mb-4">Analiz için önce giriş yapmalısınız.</p>
            <?php endif; ?>
            <form id="creditForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES); ?>">
                <div>
                    <label class="block text-sm font-medium">Aylık Gelir (₺)</label>
                    <input type="number" step="0.01" name="income" class="mt-1 block w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Kredi Tipi</label>
                    <select name="credit_type" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <option value="ihtiyaç">İhtiyaç Kredisi</option>
                        <option value="konut">Konut Kredisi</option>
                        <option value="taşıt">Taşıt Kredisi</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Vade (Ay)</label>
                        <input type="number" name="term" class="mt-1 block w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Tutar (₺)</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 block w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded" <?php echo $user ? '' : 'disabled'; ?>>Analiz Et</button>
            </form>
            <div id="analysisResult" class="mt-6 hidden">
                <h3 class="text-lg font-semibold">AI Analiz Sonuçları</h3>
                <p class="mt-2"><strong>Skor:</strong> <span id="aiScore"></span></p>
                <p><strong>Faiz Oranı Tahmini:</strong> <span id="interestRate"></span></p>
                <p><strong>Aylık Ödeme:</strong> <span id="monthlyPayment"></span></p>
                <p class="mt-2"><strong>Önerilen Bankalar:</strong></p>
                <ul id="bankList" class="list-disc list-inside text-sm text-gray-700"></ul>
                <p class="mt-4 text-sm" id="aiComment"></p>
            </div>
        </section>
        <section class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">AI Sohbet Asistanı</h2>
            <?php if (!$user): ?>
                <p class="text-sm text-red-600 mb-4">Sohbet için giriş yapmalısınız.</p>
            <?php endif; ?>
            <div class="border rounded p-4 h-64 overflow-y-auto mb-4" id="chatWindow"></div>
            <form id="chatForm" class="flex space-x-2">
                <input type="text" name="message" placeholder="Finansal sorunuzu yazın..." class="flex-1 border rounded px-3 py-2" <?php echo $user ? '' : 'disabled'; ?>>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" <?php echo $user ? '' : 'disabled'; ?>>Gönder</button>
            </form>
        </section>
    </div>
    <section class="mt-12">
        <h2 class="text-xl font-semibold mb-4">Önerilen Banka Teklifleri</h2>
        <div id="offerList" class="grid md:grid-cols-3 gap-4"></div>
    </section>
</main>
<script>
    const userToken = <?php echo $user ? json_encode($_SESSION['user']['token'] ?? Auth::generateToken($user)) : 'null'; ?>;

    async function fetchOffers() {
        if (!userToken) {
            return;
        }
        const response = await fetch('/api/offers.php', {
            headers: { 'Authorization': 'Bearer ' + userToken }
        });
        if (!response.ok) return;
        const data = await response.json();
        const container = document.getElementById('offerList');
        container.innerHTML = '';
        data.offers.forEach((offer) => {
            const card = document.createElement('div');
            card.className = 'bg-white shadow rounded p-4';
            card.innerHTML = `
                <h3 class="text-lg font-semibold">${offer.bank}</h3>
                <p>Faiz Oranı: %${offer.interest_rate}</p>
                <p>Vade: ${offer.term} ay</p>
                <p>Aylık Ödeme: ₺${offer.monthly_payment}</p>
            `;
            container.appendChild(card);
        });
    }

    document.getElementById('creditForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!userToken) return;
        const form = event.target;
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        const response = await fetch('/api/analyze.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + userToken,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();
        if (!response.ok) {
            alert(result.error || 'Analiz sırasında hata oluştu.');
            return;
        }

        document.getElementById('analysisResult').classList.remove('hidden');
        document.getElementById('aiScore').textContent = result.ai_score ?? '-';
        document.getElementById('interestRate').textContent = result.interest_rate_estimate ? '%' + result.interest_rate_estimate : '-';
        document.getElementById('monthlyPayment').textContent = result.monthly_payment ? '₺' + result.monthly_payment : '-';
        const list = document.getElementById('bankList');
        list.innerHTML = '';
        (result.recommended_banks || []).forEach((bank) => {
            const li = document.createElement('li');
            li.textContent = bank;
            list.appendChild(li);
        });
        document.getElementById('aiComment').textContent = result.ai_comment || '';
        fetchOffers();
    });

    document.getElementById('chatForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!userToken) return;
        const input = event.target.message;
        const message = input.value.trim();
        if (!message) return;

        addMessage('Kullanıcı', message);
        input.value = '';

        const response = await fetch('/api/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + userToken,
            },
            body: JSON.stringify({ message }),
        });
        const data = await response.json();
        if (!response.ok) {
            addMessage('Asistan', data.error || 'Bir hata oluştu.');
            return;
        }
        addMessage('Asistan', data.reply);
    });

    function addMessage(sender, text) {
        const container = document.getElementById('chatWindow');
        const wrapper = document.createElement('div');
        wrapper.className = 'mb-2';
        wrapper.innerHTML = `<strong>${sender}:</strong> <span class="text-sm">${text}</span>`;
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', async () => {
            await fetch('/api/logout.php');
            window.location.reload();
        });
    }

    fetchOffers();
</script>
</body>
</html>
