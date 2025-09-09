<?php
include 'config.php';
include 'csrf.php';
require_once 'mailer.php';
require_once 'audit_log.php';
session_start();

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: student_status_appointments.php'); exit; }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Invalid CSRF token'); }
$student_id = $_SESSION['user_id'];
$request_id = (int)($_POST['request_id'] ?? 0);
if ($request_id <= 0) { header('Location: student_status_appointments.php?error=bad_request'); exit; }
// Allow cancellation only for own appointment and only if pending/approved
$stmt = $conn->prepare("UPDATE appointments SET status='cancelled' WHERE id=? AND student_id=? AND status IN ('pending','approved','Pending','Approved')");
$stmt->bind_param('is', $request_id, $student_id);
$ok = $stmt->execute() && $stmt->affected_rows > 0;

if ($ok) {
    guidance_log_action($conn, $request_id, $student_id, 'cancel_by_student', '');
    // Notify counselor about cancellation
    $c = $conn->prepare("SELECT u.email, TRIM(CONCAT(u.first_name,' ',u.last_name)) AS name FROM appointments a JOIN users u ON a.user_id=u.user_id WHERE a.id=? AND a.student_id=?");
    $c->bind_param('is', $request_id, $student_id);
    $c->execute(); $cr = $c->get_result()->fetch_assoc();
    if ($cr && !empty($cr['email'])) {
        $content = '<p>Hello '.htmlspecialchars($cr['name']).',</p><p>A student has cancelled their guidance appointment (ID #'.htmlspecialchars((string)$request_id).').</p>';
        @send_branded_email($cr['email'], 'Appointment Cancelled', 'Appointment Cancelled', $content);
    }
}
$msg = $ok ? 'Appointment cancelled.' : 'Unable to cancel appointment.';
header('Location: student_status_appointments.php?success=' . urlencode($msg));
exit;
?>