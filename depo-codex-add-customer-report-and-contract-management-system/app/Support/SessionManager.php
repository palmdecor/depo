<?php

namespace App\Support;

class SessionManager
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $lifetime = (int) env('SESSION_LIFETIME', 120);
        session_set_cookie_params([
            'lifetime' => $lifetime * 60,
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax',
        ]);

        session_save_path(__DIR__ . '/../../storage/framework/sessions');

        if (!is_dir(session_save_path())) {
            mkdir(session_save_path(), 0777, true);
        }

        session_start();
    }
}
