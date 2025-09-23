<?php
declare(strict_types=1);

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        echo 'Bu işlem için yetkiniz yok.';
        exit;
    }
}

function redirect_with_message(string $location, string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
    header('Location: ' . $location);
    exit;
}

function display_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert ' . sanitize($flash['type']) . '">' . sanitize($flash['message']) . '</div>';
    }
}
