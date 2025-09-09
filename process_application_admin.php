<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == "approve") {
        $status = "Approved";
    } elseif ($action == "reject") {
        $status = "Rejected";
    } else {
        echo "Invalid action.";
        exit();
    }

    // Update application status
    $stmt = $conn->prepare("UPDATE student_room_applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $application_id);

    if ($stmt->execute()) {
        echo "Application " . $status . " successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
