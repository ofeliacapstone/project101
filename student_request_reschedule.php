<?php
include 'config.php';
include 'csrf.php';
require_once 'guidance_availability.php';
require_once 'audit_log.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: student_status_appointments.php'); exit; }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Invalid CSRF token'); }

$student_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$new_dt_raw = trim($_POST['new_datetime'] ?? '');
$note = trim($_POST['note'] ?? '');

if ($appointment_id <= 0 || $new_dt_raw === '') { header('Location: student_status_appointments.php?error=bad_request'); exit; }

try { $dt = new DateTime($new_dt_raw); } catch (Exception $e) { header('Location: student_status_appointments.php?error=invalid_datetime'); exit; }
if (!guidance_is_within_business_hours($dt)) { header('Location: student_status_appointments.php?error=outside_hours'); exit; }

// Only allow reschedule if appointment belongs to student and is approved
$chk = $conn->prepare("SELECT status FROM appointments WHERE id=? AND student_id=?");
$chk->bind_param('is', $appointment_id, $student_id);
$chk->execute(); $row = $chk->get_result()->fetch_assoc();
if (!$row || strtolower($row['status']) !== 'approved') { header('Location: student_status_appointments.php?error=not_allowed'); exit; }

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS reschedule_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  student_id VARCHAR(64) NOT NULL,
  requested_datetime DATETIME NOT NULL,
  note TEXT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'open',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX(appointment_id), INDEX(student_id), INDEX(status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Upsert: close previous open requests for this appointment, then insert new
$conn->query("UPDATE reschedule_requests SET status='closed' WHERE appointment_id=".(int)$appointment_id." AND status='open'");
$ins = $conn->prepare("INSERT INTO reschedule_requests (appointment_id, student_id, requested_datetime, note) VALUES (?,?,?,?)");
$fmt = $dt->format('Y-m-d H:i:00');
$ins->bind_param('isss', $appointment_id, $student_id, $fmt, $note);
$ok = $ins->execute();

if ($ok) { guidance_log_action($conn, $appointment_id, $student_id, 'reschedule_requested', json_encode(['requested'=>$fmt,'note'=>$note])); }

header('Location: student_status_appointments.php?success=' . urlencode($ok ? 'Reschedule request sent.' : 'Failed to send reschedule request.'));
exit;
?>