<?php
require_once __DIR__ . '/config.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('Geçersiz istek.');
}

$query = 'SELECT cr.id, cr.user_id, cr.original_name, cr.stored_name FROM customer_reports cr';
if (is_admin()) {
    $query .= ' WHERE cr.id = ? LIMIT 1';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
} else {
    $query .= ' WHERE cr.id = ? AND cr.user_id = ? LIMIT 1';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $id, $_SESSION['user']['id']);
}

$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
$stmt->close();

if (!$report) {
    http_response_code(404);
    exit('Rapor bulunamadı.');
}

$filePath = __DIR__ . '/uploads/' . $report['stored_name'];
if (!is_file($filePath)) {
    http_response_code(404);
    exit('Dosya bulunamadı.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($report['original_name']) . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
