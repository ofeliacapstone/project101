<?php
session_start();
require_once 'config.php'; // Ensure this file correctly connects to your database

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in to apply for a room."]);
    exit;
}

if (!isset($_SESSION['dormitory_policy_accepted']) || $_SESSION['dormitory_policy_accepted'] !== true) {
    header('Location: dormitory_policy.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$status = 'Pending';
$applied_at = date('Y-m-d H:i:s');

if ($room_id == 0) {
    echo json_encode(["success" => false, "message" => "Invalid room selection."]);
    exit;
}

// Check if room exists
$roomQuery = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($roomQuery);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Selected room does not exist."]);
    exit;
}

// Check if user exists in users table
$userQuery = "SELECT user_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User account is invalid."]);
    exit;
}

// Check if the user has already applied for this room
$checkApplication = "SELECT * FROM student_room_applications WHERE user_id = ? AND room_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($checkApplication);
$stmt->bind_param("si", $user_id, $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "You have already applied for this room."]);
    exit;
}

// Insert the room application
$insertQuery = "INSERT INTO student_room_applications (user_id, room_id, message, status, applied_at) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sisss", $user_id, $room_id, $message, $status, $applied_at);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Application submitted successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to submit application."]);
}
exit;
?>
