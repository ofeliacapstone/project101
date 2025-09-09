<?php
require_once 'config.php'; // Database connection
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_list.php");
    exit();
}

$admin_id = $_GET['id'];

// Check if the user exists and is not Power Admin
$stmt_check = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt_check->bind_param("s", $admin_id);
$stmt_check->execute();
$result = $stmt_check->get_result();
$user = $result->fetch_assoc();
$stmt_check->close();

if (!$user) {
    echo "Admin not found.";
    exit();
}

// Prevent deletion of Power Admin
if ($user['role'] === 'Power Admin') {
    echo "You cannot delete the Power Admin.";
    exit();
}

// Delete admin
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("s", $admin_id);

if ($stmt->execute()) {
    header("Location: admin_list.php?deleted=success");
    exit();
} else {
    echo "Error deleting admin: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
