<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['document_id'])) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Missing document ID']); exit(); }
if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Invalid CSRF token']); exit(); }
$docId = (int)$_POST['document_id'];

try {
	// Fetch document and validate path
	$st = $conn->prepare("SELECT id, application_id, file_path FROM scholarship_documents WHERE id=?");
	$st->bind_param("i", $docId);
	$st->execute();
	$doc = $st->get_result()->fetch_assoc();
	$st->close();
	if (!$doc) throw new Exception('Document not found.');
	$real = realpath(__DIR__ . '/' . $doc['file_path']);
	$base = realpath(__DIR__ . '/uploads/scholarship_documents');
	if ($real===false || $base===false || strpos($real, $base)!==0) throw new Exception('Invalid file path.');
	// Delete DB record first (to avoid orphaning)
	$del = $conn->prepare("DELETE FROM scholarship_documents WHERE id=?");
	$del->bind_param("i", $docId);
	if (!$del->execute()) { $del->close(); throw new Exception('Failed to delete record.'); }
	$del->close();
	// Attempt to remove physical file (ignore if already missing)
	if (is_file($real)) @unlink($real);
	echo json_encode(['status'=>'success','message'=>'Document deleted']);
} catch (Exception $e) {
	http_response_code(400);
	echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}