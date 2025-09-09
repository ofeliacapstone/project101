<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit('Forbidden'); }
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { http_response_code(400); exit('Bad request'); }

$stmt = $conn->prepare("SELECT student_id, receipt_path FROM payments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) { http_response_code(404); exit('Not found'); }

$canView = ($_SESSION['user_id'] === $row['student_id']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'Dormitory Admin');
if (!$canView) { http_response_code(403); exit('Forbidden'); }

$uploadDir = __DIR__ . '/uploads/payments';
$filePath = $uploadDir . '/' . basename($row['receipt_path']);
if (!is_file($filePath)) { http_response_code(404); exit('File missing'); }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($filePath);
$allowed = ['image/jpeg','image/png','application/pdf'];
if (!in_array($mime, $allowed, true)) { $mime = 'application/octet-stream'; }

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="payment-' . $id . '"');
readfile($filePath);
exit;
