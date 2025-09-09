<?php
require 'config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Invalid CSRF token']); exit; }
require_once 'mailer.php';
require_once 'guidance_availability.php';
require_once 'audit_log.php';
$id = (int)($_POST['id'] ?? 0);
$start = $_POST['start'] ?? '';
$acting_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
if (!$id || $start===''){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'Bad request']); exit; }
try{ $dt=new DateTime($start); } catch(Exception $e){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid date']); exit; }
$date=$dt->format('Y-m-d H:i:s');
// Business hours check

if (!guidance_is_within_business_hours(new DateTime($date)) || guidance_is_blackout($conn, new DateTime($date))) { echo json_encode(['success'=>false,'message'=>'Outside business hours or on a blackout day.']); exit; }
// Permission: allow admin or owning counselor only
$own = $conn->prepare("SELECT user_id FROM appointments WHERE id = ?");
$own->bind_param('i', $id);
$own->execute();
$ownerRow = $own->get_result()->fetch_assoc();
if (!$ownerRow) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not found']); exit; }
if ($role !== 'Guidance Admin' && $ownerRow['user_id'] !== $acting_user_id) { http_response_code(403); echo json_encode(['success'=>false,'message':'Forbidden']); exit; }
// Conflict check (1 hour window)
$chk=$conn->prepare("SELECT COUNT(*) AS c FROM appointments WHERE user_id=? AND id<>? AND appointment_date < ? AND DATE_ADD(appointment_date, INTERVAL 1 HOUR) > ?");
$chk->bind_param('siss', $ownerRow['user_id'], $id, $date, $date);
$chk->execute();
$c=$chk->get_result()->fetch_assoc()['c'] ?? 0;
if ($c > 0) { echo json_encode(['success'=>false,'message'=>'Time slot conflict']); exit; }
$stmt=$conn->prepare("UPDATE appointments SET appointment_date=? WHERE id=? AND status IN ('pending','approved','Pending','Approved')");
$stmt->bind_param('si', $date, $id);
$ok=$stmt->execute();
if ($ok) {
  guidance_log_action($conn, $id, $_SESSION['user_id'], 'reschedule_drag', json_encode(['new'=>$date]));
  // Notify student about reschedule
  $stu = $conn->prepare("SELECT u.email, TRIM(CONCAT(u.first_name,' ',u.last_name)) AS name FROM appointments a JOIN users u ON a.student_id=u.user_id WHERE a.id=?");
  $stu->bind_param('i', $id);
  $stu->execute(); $s = $stu->get_result()->fetch_assoc();
  if ($s && !empty($s['email'])) {
    $ics = ics_download_link($id, $date);
    $content = '<p>Hello '.htmlspecialchars($s['name']).',</p><p>Your guidance appointment time has been updated.</p><p><strong>New Date & Time:</strong> '.htmlspecialchars($date).'</p><p><a href="'.htmlspecialchars($ics).'" style="background:#0d6efd;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;">Add to Calendar</a></p>';
    @send_branded_email($s['email'], 'Appointment Rescheduled', 'Appointment Rescheduled', $content);
  }
}
echo json_encode(['success'=>$ok, 'message'=>$ok?'Updated':'Update failed']);