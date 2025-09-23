<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$validSections = ['profile', 'reports'];
$section = $_GET['section'] ?? ($_POST['section'] ?? 'profile');
if (!in_array($section, $validSections, true)) {
    $section = 'profile';
}

$profileErrors = [];
$profileSuccess = '';
$reportErrors = [];
$reportSuccess = '';

$memberNameValue = '';
$memberEmailValue = '';

if (!isset($_SESSION['user']['profile'])) {
    $_SESSION['user']['profile'] = [
        'first_name' => 'Demo',
        'last_name' => 'Kullanıcı',
    ];
}

if (!isset($_SESSION['user']['password_hash'])) {
    $_SESSION['user']['password_hash'] = null;
}

if (!isset($_SESSION['reports']) || !is_array($_SESSION['reports'])) {
    $_SESSION['reports'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $section = 'profile';

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($firstName === '') {
            $profileErrors[] = 'Ad alanı boş bırakılamaz.';
        }

        if ($lastName === '') {
            $profileErrors[] = 'Soyad alanı boş bırakılamaz.';
        }

        $newPassword = trim($newPassword);
        $confirmPassword = trim($confirmPassword);

        if ($newPassword !== '' || $confirmPassword !== '') {
            if ($newPassword === '' || $confirmPassword === '') {
                $profileErrors[] = 'Yeni şifre ve tekrar alanları birlikte doldurulmalıdır.';
            } elseif ($newPassword !== $confirmPassword) {
                $profileErrors[] = 'Yeni şifre ile şifre tekrarı eşleşmiyor.';
            } elseif (strlen($newPassword) < 6) {
                $profileErrors[] = 'Yeni şifre en az 6 karakter olmalıdır.';
            }
        }

        if (!$profileErrors) {
            $_SESSION['user']['profile']['first_name'] = $firstName;
            $_SESSION['user']['profile']['last_name'] = $lastName;

            if ($newPassword !== '') {
                $_SESSION['user']['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $profileSuccess = 'Profil bilgileriniz güncellendi.';
        }
    } elseif ($action === 'upload_report') {
        $section = 'reports';

        $memberNameValue = trim($_POST['member_name'] ?? '');
        $memberEmailValue = trim($_POST['member_email'] ?? '');

        if ($memberNameValue === '') {
            $reportErrors[] = 'Üye adı alanı boş bırakılamaz.';
        }

        if ($memberEmailValue === '') {
            $reportErrors[] = 'Üye e-posta alanı boş bırakılamaz.';
        } elseif ($memberEmailValue !== '' && !filter_var($memberEmailValue, FILTER_VALIDATE_EMAIL)) {
            $reportErrors[] = 'Geçerli bir e-posta adresi giriniz.';
        }

        if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $reportErrors[] = 'Yüklenecek bir PDF dosyası seçiniz.';
        }

        if (!$reportErrors && isset($_FILES['report_file'])) {
            $file = $_FILES['report_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $reportErrors[] = 'Dosya yüklenirken bir hata oluştu. Lütfen tekrar deneyin.';
            } else {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $sizeLimit = 5 * 1024 * 1024; // 5 MB

                if ($extension !== 'pdf') {
                    $reportErrors[] = 'Yalnızca PDF formatındaki dosyalar yüklenebilir.';
                } elseif ($file['size'] > $sizeLimit) {
                    $reportErrors[] = 'Dosya boyutu 5 MB sınırını aşmamalıdır.';
                }

                if (!$reportErrors) {
                    $uploadsDirectory = __DIR__ . '/uploads';

                    if (!is_dir($uploadsDirectory) && !mkdir($uploadsDirectory, 0775, true) && !is_dir($uploadsDirectory)) {
                        $reportErrors[] = 'Yükleme klasörü oluşturulamadı.';
                    } else {
                        $storedFileName = uniqid('report_', true) . '.pdf';
                        $destination = $uploadsDirectory . '/' . $storedFileName;

                        if (!move_uploaded_file($file['tmp_name'], $destination)) {
                            $reportErrors[] = 'Dosya kaydedilirken bir hata oluştu.';
                        } else {
                            $_SESSION['reports'][] = [
                                'member_name' => $memberNameValue,
                                'member_email' => $memberEmailValue,
                                'original_name' => $file['name'],
                                'stored_name' => $storedFileName,
                                'uploaded_at' => time(),
                            ];

                            $reportSuccess = 'Rapor başarıyla yüklendi.';
                            $memberNameValue = '';
                            $memberEmailValue = '';
                        }
                    }
                }
            }
        }
    }
}

$user = $_SESSION['user'];
$profile = $user['profile'];
$reports = $_SESSION['reports'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="container dashboard">
        <aside class="sidebar">
            <h2>Kontrol Paneli</h2>
            <nav>
                <ul>
                    <li class="<?= $section === 'profile' ? 'active' : '' ?>">
                        <a href="dashboard.php?section=profile">Profil Ayarları</a>
                    </li>
                    <li class="<?= $section === 'reports' ? 'active' : '' ?>">
                        <a href="dashboard.php?section=reports">Raporlarım</a>
                    </li>
                    <li class="disabled"><span>Güvenlik</span></li>
                    <li class="disabled"><span>Bildirimler</span></li>
                </ul>
            </nav>
            <form method="post" action="logout.php">
                <button type="submit" class="secondary">Çıkış Yap</button>
            </form>
        </aside>
        <main class="content">
            <h1>Hoş geldin, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p class="meta">Giriş zamanı: <?= date('d.m.Y H:i', $user['login_time']) ?></p>

            <?php if ($section === 'profile' && $profileSuccess): ?>
                <div class="alert success"><?= htmlspecialchars($profileSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($section === 'profile' && $profileErrors): ?>
                <div class="alert">
                    <ul>
                        <?php foreach ($profileErrors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($section === 'profile'): ?>
                <section class="card">
                    <h2>Profil Bilgileri</h2>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="section" value="profile">
                        <div class="form-grid">
                            <div class="form-control">
                                <label for="first_name">Ad</label>
                                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="form-control">
                                <label for="last_name">Soyad</label>
                                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>

                        <div class="form-control">
                            <label for="new_password">Yeni Şifre</label>
                            <input type="password" id="new_password" name="new_password" placeholder="Yeni şifre belirleyin">
                        </div>
                        <div class="form-control">
                            <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Yeni şifreyi tekrar girin">
                        </div>

                        <button type="submit">Profili Güncelle</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($section === 'reports' && $reportSuccess): ?>
                <div class="alert success"><?= htmlspecialchars($reportSuccess, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($section === 'reports' && $reportErrors): ?>
                <div class="alert">
                    <ul>
                        <?php foreach ($reportErrors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($section === 'reports'): ?>
                <section class="card">
                    <h2>Yeni Üye Raporu Yükle</h2>
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_report">
                        <input type="hidden" name="section" value="reports">
                        <div class="form-grid">
                            <div class="form-control">
                                <label for="member_name">Üye Adı</label>
                                <input type="text" id="member_name" name="member_name" value="<?= htmlspecialchars($memberNameValue, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="form-control">
                                <label for="member_email">Üye E-posta</label>
                                <input type="email" id="member_email" name="member_email" value="<?= htmlspecialchars($memberEmailValue, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>

                        <div class="form-control">
                            <label for="report_file">Rapor PDF</label>
                            <input type="file" id="report_file" name="report_file" accept="application/pdf" required>
                            <p class="hint">PDF formatında en fazla 5 MB boyutunda dosya yükleyebilirsiniz.</p>
                        </div>

                        <button type="submit">Raporu Yükle</button>
                    </form>
                </section>

                <section class="card">
                    <h2>Yüklenen Raporlar</h2>
                    <?php if ($reports): ?>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Üye</th>
                                        <th>E-posta</th>
                                        <th>Dosya Adı</th>
                                        <th>Yüklenme Tarihi</th>
                                        <th>İndir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_reverse($reports) as $report): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($report['member_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($report['member_email'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($report['original_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= date('d.m.Y H:i', $report['uploaded_at']) ?></td>
                                            <td>
                                                <a class="link" href="uploads/<?= rawurlencode($report['stored_name']) ?>" target="_blank" rel="noopener">
                                                    Görüntüle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Henüz yüklenmiş bir rapor bulunmuyor.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
