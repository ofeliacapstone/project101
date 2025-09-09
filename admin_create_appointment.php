<?php
require 'config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Invalid CSRF token']); exit; }
require_once 'mailer.php';
require_once 'guidance_availability.php';
require_once 'audit_log.php';
$student_id = $_POST['student_id'] ?? '';
$counselor_id = $_POST['counselor_id'] ?? '';
$datetime = $_POST['datetime'] ?? '';
$reason = trim($_POST['reason'] ?? '');
if ($student_id === '' || $counselor_id === '' || $datetime === '') { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing fields']); exit; }
try { $dt=new DateTime($datetime); } catch(Exception $e){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid date/time']); exit; }
$startStr = $dt->format('Y-m-d H:i:00');
$endStr = $dt->modify('+1 hour')->format('Y-m-d H:i:00');

// Business hours check
if (!guidance_is_within_business_hours(new DateTime($startStr)) || guidance_is_blackout($conn, new DateTime($startStr))) {
  echo json_encode(['success'=>false,'message'=>'Outside business hours (Mon–Fri, 08:00–17:00).']);
  exit;
}
// Conflict check for counselor
$chk=$conn->prepare("SELECT COUNT(*) AS c FROM appointments WHERE user_id=? AND appointment_date < ? AND DATE_ADD(appointment_date, INTERVAL 1 HOUR) > ?");
$chk->bind_param('sss', $counselor_id, $startStr, $endStr);
$chk->execute(); $c=$chk->get_result()->fetch_assoc()['c'] ?? 0;
if ($c > 0) { echo json_encode(['success'=>false,'message'=>'Counselor slot is already booked.']); exit; }
$stmt=$conn->prepare("INSERT INTO appointments (student_id, user_id, appointment_date, reason, status) VALUES (?, ?, ?, ?, 'approved')");
$stmt->bind_param('ssss', $student_id, $counselor_id, $startStr, $reason);
$ok=$stmt->execute();
if ($ok) {
  guidance_log_action($conn, (int)$conn->insert_id, $_SESSION['user_id'], 'create_appointment', json_encode(['student_id'=>$student_id,'start'=>$startStr]));
  // Notify student
  $stu = $conn->prepare("SELECT email, TRIM(CONCAT(first_name,' ',last_name)) AS name FROM users WHERE user_id=?");
  $stu->bind_param('s', $student_id);
  $stu->execute(); $stuRes = $stu->get_result()->fetch_assoc();
  $email = $stuRes['email'] ?? '';
  if ($email) {
    $ics = ics_download_link((int)$conn->insert_id, $startStr);
    $content = '<p>Hello '.htmlspecialchars($stuRes['name'] ?? $student_id).',</p><p>Your guidance appointment has been created and approved.</p><p><strong>Date & Time:</strong> '.htmlspecialchars($startStr).'</p><p><a href="'.htmlspecialchars($ics).'" style="background:#0d6efd;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;">Add to Calendar</a></p><p>If you need to reschedule, please coordinate with your counselor.</p>';
    @send_branded_email($email, 'Appointment Confirmed', 'Appointment Confirmed', $content);
  }
}
echo json_encode(['success'=>$ok, 'message'=>$ok?'Appointment created.':'Failed to create appointment']);