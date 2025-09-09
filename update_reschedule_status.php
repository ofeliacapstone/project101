<?php
include 'config.php';
include 'csrf.php';
require_once 'audit_log.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) { http_response_code(403); exit('Unauthorized'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Invalid CSRF token'); }

$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$status = ($_POST['status'] ?? '');
if (!$appointment_id || !in_array($status, ['open','closed'], true)) { http_response_code(400); exit('Bad request'); }

$conn->query("CREATE TABLE IF NOT EXISTS reschedule_requests (id INT AUTO_INCREMENT PRIMARY KEY, appointment_id INT NOT NULL, student_id VARCHAR(64) NOT NULL, requested_datetime DATETIME NOT NULL, note TEXT NULL, status VARCHAR(16) NOT NULL DEFAULT 'open', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX(appointment_id), INDEX(status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$stmt = $conn->prepare('UPDATE reschedule_requests SET status=? WHERE appointment_id=? AND status<>?');
$stmt->bind_param('sis', $status, $appointment_id, $status);
$ok = $stmt->execute();
if ($ok) { guidance_log_action($conn, $appointment_id, $_SESSION['user_id'], 'reschedule_'.$status, ''); }
echo $ok ? 'OK' : 'ERR';
?>
