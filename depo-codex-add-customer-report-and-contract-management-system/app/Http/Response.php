<?php

namespace App\Http;

class Response
{
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public static function download(string $filePath, string $fileName): void
    {
        if (!is_file($filePath)) {
            http_response_code(404);
            exit('Dosya bulunamadı.');
        }

        $cleanName = str_replace(['"', "'", '\\'], '', basename($fileName));
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $cleanName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public static function pdf(string $content, string $fileName, string $disposition = 'attachment'): void
    {
        $cleanName = str_replace(['"', "'", '\\'], '', basename($fileName));
        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . $disposition . '; filename="' . $cleanName . '"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }
}
