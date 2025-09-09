<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    // Ensure ID is a valid integer
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        die("Invalid application ID.");
    }

    if ($action == 'approve') {
        $status = 'approved';
        $approval_date = date('Y-m-d H:i:s'); // Current date and time
        $stmt = $conn->prepare("UPDATE scholarship_applications SET status = ?, approval_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $approval_date, $id);
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $stmt = $conn->prepare("UPDATE scholarship_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM scholarship_applications WHERE id = ?");
        $stmt->bind_param("i", $id);
    } else {
        die("Invalid action.");
    }

    if ($stmt->execute()) {
        echo "Action performed successfully!";
    } else {
        echo "Error performing action: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request.");
}
?>