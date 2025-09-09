<?php
include 'config.php'; 
session_start();
// Require role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) {
    header('Location: login.php');
    exit;
}
// If form is submitted to delete a request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_request'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    $request_id = (int)($_POST['request_id'] ?? 0);
    $acting_user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'] ?? '';

    if ($request_id > 0) {
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

        $deleteQuery = "DELETE FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $request_id);
        if ($stmt->execute()) {
            header("Location: guidance_list_admin.php?success=" . urlencode('Guidance request deleted successfully'));
            exit();
        } else {
            echo 'Delete failed. Try again!';
        }
    } else {
        echo 'Invalid data. Please try again.';
    }
}
?>