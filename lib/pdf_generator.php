<?php
declare(strict_types=1);

function pdf_escape_text(string $text): string
{
    $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    return $text;
}

function convert_text_for_pdf(string $text): string
{
    if (function_exists('mb_convert_encoding')) {
        $converted = @mb_convert_encoding($text, 'Windows-1254', 'UTF-8');
        if ($converted !== false) {
            return $converted;
        }
    }

    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'Windows-1254//TRANSLIT', $text);
        if ($converted !== false) {
            return $converted;
        }
    }

    return $text;
}

function output_contract_pdf(string $text, string $fileName): void
{
    $lines = preg_split("/(\r\n|\r|\n)/", $text);
    if ($lines === false) {
        $lines = [$text];
    }

    $content = "BT\n/F1 11 Tf\n14 TL\n1 0 0 1 50 780 Tm\n";
    foreach ($lines as $line) {
        $encodedLine = pdf_escape_text(convert_text_for_pdf($line));
        $content .= '(' . $encodedLine . ") Tj\nT*\n";
    }
    $content .= "ET\n";

    $contentLength = strlen($content);

    $objects = [];
    $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
    $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
    $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
    $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n";
    $objects[] = "5 0 obj\n<< /Length $contentLength >>\nstream\n$content\nendstream\nendobj\n";

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    $position = strlen($pdf);
    foreach ($objects as $object) {
        $offsets[] = $position;
        $pdf .= $object;
        $position = strlen($pdf);
    }

    $xrefPosition = strlen($pdf);
    $pdf .= 'xref\n0 ' . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    foreach ($offsets as $offset) {
        $pdf .= sprintf('%010d 00000 n %s', $offset, "\n");
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}
