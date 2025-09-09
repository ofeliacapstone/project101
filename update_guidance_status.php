<?php
include 'config.php'; 
session_start();
require_once 'mailer.php';
require_once 'audit_log.php';

// Require role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) {
    header('Location: login.php');
    exit;
}
// If form is submitted to update status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
    $request_id = (int)($_POST['request_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $admin_message = trim($_POST['admin_message'] ?? '');
    $acting_user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'] ?? '';
    $allowed = ['pending','approved','completed','rejected','cancelled'];
    $status_lower = strtolower($status);
    if ($request_id > 0 && in_array($status_lower, $allowed, true)) {
        // Ownership/permission check
        $own = $conn->prepare("SELECT user_id FROM appointments WHERE id = ?");
        $own->bind_param('i', $request_id);
        $own->execute();
        $ownerRow = $own->get_result()->fetch_assoc();
        if (!$ownerRow) { echo 'Invalid data. Please try again.'; exit; }
        if ($role !== 'Guidance Admin' && $ownerRow['user_id'] !== $acting_user_id) {
            http_response_code(403);
            exit('Forbidden');
        }
        $updateQuery = "UPDATE appointments SET status = ?, admin_message = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $status_lower, $admin_message, $request_id);
        $notify = true;

        if ($stmt->execute()) {
            guidance_log_action($conn, $request_id, $acting_user_id, 'status_update', json_encode(['status'=>$status_lower,'message'=>$admin_message]));
            // Notify student about status change
            $stu = $conn->prepare("SELECT u.email, TRIM(CONCAT(u.first_name,' ',u.last_name)) AS name FROM appointments a JOIN users u ON a.student_id=u.user_id WHERE a.id=?");
            $stu->bind_param('i', $request_id);
            $stu->execute(); $s = $stu->get_result()->fetch_assoc();
            if ($s && !empty($s['email'])) {
                $content = '<p>Hello '.htmlspecialchars($s['name']).',</p><p>Your guidance request status has been updated to <strong>'.htmlspecialchars(ucfirst($status_lower)).'</strong>.</p>'.($admin_message?('<p>Message: '.htmlspecialchars($admin_message).'</p>'):'');
                @send_branded_email($s['email'], 'Guidance Request Updated', 'Guidance Request Updated', $content);
            }
            header("Location: guidance_list_admin.php?success=" . urlencode('Guidance request updated successfully'));
            exit();
        } else {
            echo 'Update failed. Try again!';
        }
    } else {
        echo 'Invalid data. Please try again.';
    }
}
?>