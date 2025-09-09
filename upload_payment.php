<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit;
}

$studentId = $_SESSION['user_id'];
$roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';

if ($roomId <= 0) { echo json_encode(["success" => false, "message" => "No active room assignment found."]); exit; }
if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) { echo json_encode(["success" => false, "message" => "Invalid amount."]); exit; }
if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) { echo json_encode(["success" => false, "message" => "Invalid file upload."]); exit; }

$maxSize = 5 * 1024 * 1024;
if ($_FILES['receipt']['size'] > $maxSize) { echo json_encode(["success" => false, "message" => "File too large. Max 5MB."]); exit; }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['receipt']['tmp_name']);
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','application/pdf'=>'pdf'];
if (!array_key_exists($mime, $allowed)) { echo json_encode(["success" => false, "message" => "Invalid file type."]); exit; }

$ext = $allowed[$mime];
$uploadDir = __DIR__ . '/uploads/payments';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
$timestamp = date('Ymd_His');
$sanitizedId = preg_replace('/[^A-Za-z0-9_-]/','', $studentId);
$filename = $sanitizedId . '_' . $timestamp . '.' . $ext;
$dest = $uploadDir . '/' . $filename;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $dest)) {
    echo json_encode(["success" => false, "message" => "Failed to save file."]); exit;
}

$stmt = $conn->prepare("INSERT INTO payments (student_id, room_id, amount, receipt_path, status, submitted_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param('sids', $studentId, $roomId, $amount, $filename);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Payment uploaded. Awaiting verification."]);
} else {
    @unlink($dest);
    echo json_encode(["success" => false, "message" => "Database error."]); 
}
?>