<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->bind_param("ss", $new_status, $user_id);
    
    if ($stmt->execute()) {
        header("Location: power_admin_users.php?success=status_updated");
        exit();
    } else {
        header("Location: power_admin_users.php?error=update_failed");
        exit();
    }
}
?>
