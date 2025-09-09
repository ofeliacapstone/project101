<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Not authenticated']); exit(); }
if ($_SERVER['REQUEST_METHOD']!=='POST' || !isset($_POST['application_id'])) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Invalid request']); exit(); }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Invalid CSRF token']); exit(); }
$appId = (int)$_POST['application_id'];
$userId = $_SESSION['user_id'];
$reason = trim($_POST['reason'] ?? 'Withdrawn by student');

// Ensure ownership and pending status
$st = $conn->prepare("SELECT sa.id, sa.status, sa.scholarship_id, s.name AS scholarship_name FROM scholarship_applications sa JOIN scholarships s ON s.id=sa.scholarship_id WHERE sa.id=? AND sa.user_id=?");
$st->bind_param("is", $appId, $userId);
$st->execute();
$app = $st->get_result()->fetch_assoc();
$st->close();
if (!$app) { http_response_code(404); echo json_encode(['status'=>'error','message'=>'Application not found']); exit(); }
if (strtolower($app['status'])!=='pending') { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Only pending applications can be withdrawn']); exit(); }

$conn->begin_transaction();
try {
	$up = $conn->prepare("UPDATE scholarship_applications SET status='withdrawn', rejection_reason=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
	$by = $userId; // tracked in reviewed_by
	$up->bind_param("ssi", $reason, $by, $appId);
	$up->execute();
	$up->close();

	$dec = $conn->prepare("UPDATE scholarships SET current_applicants = GREATEST(0, current_applicants - 1) WHERE id=?");
	$dec->bind_param("i", $app['scholarship_id']);
	$dec->execute();
	$dec->close();

	// Notify student (self confirmation)
	$ns = $conn->prepare("INSERT INTO scholarship_notifications (user_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'info', 0, NOW())");
	$title = 'Application Withdrawn';
	$msg = 'You withdrew your application for '.$app['scholarship_name'].'. Reason: '.$reason;
	$ns->bind_param("sss", $userId, $title, $msg);
	$ns->execute();
	$ns->close();

	$conn->commit();
	echo json_encode(['status'=>'success']);
} catch (Exception $e) {
	$conn->rollback();
	http_response_code(500);
	echo json_encode(['status'=>'error','message'=>'Failed to withdraw']);
}