<?php
session_start();
require_once 'config.php';

// Check admin session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Dormitory Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Validate inputs
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$admin_id = $_SESSION['user_id'];
$allowedActions = ['verify', 'reject'];

if ($id < 1 || !in_array($action, $allowedActions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Fetch payment status for audit
$stmt = $conn->prepare("SELECT status FROM payments WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Payment not found']);
    exit;
}
$payment = $result->fetch_assoc();
$old_status = $payment['status'];

// Set new status
$new_status = $action === 'verify' ? 'Verified' : 'Rejected';
$remarks = $action === 'reject' ? trim($_POST['remarks'] ?? '') : null;
if ($action === 'reject' && !$remarks) {
    echo json_encode(['success' => false, 'message' => 'Remarks required for rejection']);
    exit;
}

// Update payment
$stmt = $conn->prepare("UPDATE payments SET status = ?, remarks = ? WHERE id = ?");
$stmt->bind_param('ssi', $new_status, $remarks, $id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Update failed.']);
    exit;
}

// Audit log
$stmt = $conn->prepare("INSERT INTO payment_audit_logs (payment_type, payment_id, action, old_status, new_status, admin_id, remarks, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
$payment_type = 'Dorm';
$stmt->bind_param('sisssss', $payment_type, $id, $action, $old_status, $new_status, $admin_id, $remarks);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Payment ' . $action . 'ed successfully.']);
exit;
?>