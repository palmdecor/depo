<?php /** @var callable $content */ ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataHub - Giriş</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($view->asset('assets/css/style.css')) ?>">
</head>
<body class="auth-body">
<main class="auth-container">
    <?php $content(); ?>
</main>
</body>
</html>
