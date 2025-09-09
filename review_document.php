<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['status'=>'error','message'=>'Method not allowed']); exit(); }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Invalid CSRF token']); exit(); }
$docId = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
$action = $_POST['action'] ?? '';
$value = isset($_POST['value']) ? (int)$_POST['value'] : 0;
if ($docId <= 0 || !in_array($action, ['verify','flag'], true)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Invalid parameters']); exit(); }

// Create review table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS scholarship_document_reviews (
  doc_id INT PRIMARY KEY,
  verified TINYINT(1) DEFAULT 0,
  flagged TINYINT(1) DEFAULT 0,
  tags VARCHAR(255) DEFAULT NULL,
  reviewed_by VARCHAR(50) DEFAULT NULL,
  reviewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$col = $action === 'verify' ? 'verified' : 'flagged';
$uid = $_SESSION['user_id'];
// Upsert
$stmt = $conn->prepare("INSERT INTO scholarship_document_reviews (doc_id, $col, reviewed_by) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE $col=VALUES($col), reviewed_by=VALUES(reviewed_by), reviewed_at=CURRENT_TIMESTAMP");
$stmt->bind_param('iis', $docId, $value, $uid);
$ok = $stmt->execute();
$stmt->close();
if (!$ok) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'Failed to update']); exit(); }

echo json_encode(['status'=>'success']);