<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Forbidden');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { http_response_code(400); exit('Bad request'); }

$stmt = $conn->prepare("SELECT p.user_id, p.receipt_file FROM dormitory_payments p WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$rec = $res->fetch_assoc();
if (!$rec) { http_response_code(404); exit('Not found'); }

$canView = false;
if ($_SESSION['user_id'] === $rec['user_id']) { $canView = true; }
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Dormitory Admin') { $canView = true; }
if (!$canView) { http_response_code(403); exit('Forbidden'); }

$uploadDir = __DIR__ . '/uploads/payments';
$filePath = $uploadDir . '/' . basename($rec['receipt_file']);
if (!is_file($filePath)) { http_response_code(404); exit('File missing'); }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($filePath);
$allowed = ['image/jpeg','image/png','application/pdf'];
if (!in_array($mime, $allowed, true)) { $mime = 'application/octet-stream'; }

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="receipt-' . $id . '"');
readfile($filePath);
exit;
