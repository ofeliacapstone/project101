<?php
session_start();
require_once 'config.php';
if (!isset($_GET['id'])) { http_response_code(400); echo 'Missing document ID'; exit(); }
$docId = (int)$_GET['id'];

$sql = "SELECT d.file_path, d.file_name, d.mime_type, a.user_id AS owner_user_id
        FROM scholarship_documents d
        JOIN scholarship_applications a ON a.id=d.application_id
        WHERE d.id=?";
$st = $conn->prepare($sql);
$st->bind_param("i", $docId);
$st->execute();
$doc = $st->get_result()->fetch_assoc();
$st->close();
if (!$doc) { http_response_code(404); echo 'Document not found'; exit(); }

$isAdmin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['Scholarship Admin','Admin']);
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $doc['owner_user_id'];
if (!$isAdmin && !$isOwner) { http_response_code(403); echo 'Not authorized'; exit(); }

$real = realpath(__DIR__ . '/' . $doc['file_path']);
$base = realpath(__DIR__ . '/uploads/scholarship_documents');
if ($real===false || strpos($real, $base)!==0) { http_response_code(400); echo 'Invalid path'; exit(); }
if (!is_file($real)) { http_response_code(404); echo 'File missing'; exit(); }

$mime = $doc['mime_type'] ?: 'application/octet-stream';
header('Content-Type: '.$mime);
header('Content-Length: ' . filesize($real));
header('Content-Disposition: inline; filename="'.basename($doc['file_name']).'"');
readfile($real);
