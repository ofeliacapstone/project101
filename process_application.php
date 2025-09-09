<?php
session_start();
include 'config.php'; // Make sure this path is correct

// ✅ Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'DormitoryAdmin') {
    header("Location: ../login.php");
    exit();
}

// ✅ Check if request is valid
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Error: This page only accepts POST requests.");
}

if (!isset($_POST['application_id'], $_POST['action'])) {
    die("Error: Missing required form fields.");
}

$application_id = intval($_POST['application_id']);
$action = $_POST['action'];

// ✅ Debugging: Print request data
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit();
*/

// ✅ Fetch application details
$sql = "SELECT * FROM student_room_applications WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();

if (!$application) {
    die("Error: Application not found.");
}

$user_id = $application['user_id'];
$room_id = $application['room_id'];

if ($action == "approve") {
    // ✅ Check room availability
    $sql = "SELECT total_beds, occupied_beds FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if (!$room) {
        die("Error: Room does not exist.");
    }

    if ($room['occupied_beds'] >= $room['total_beds']) {
        die("Error: Room is full. Cannot approve.");
    }

    // ✅ Approve application & update room assignment
    $conn->begin_transaction();
    try {
        // Update application status
        $sql = "UPDATE student_room_applications SET status = 'Approved' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $application_id);
        $stmt->execute();

        // Assign student to room
        $sql = "INSERT INTO student_room_assignments (user_id, room_id, status) VALUES (?, ?, 'Active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_id, $room_id);
        $stmt->execute();

        // Update room occupancy
        $sql = "UPDATE rooms SET occupied_beds = occupied_beds + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        $conn->commit();
        header("Location: admin_room_applications.php?message=Application Approved");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: Approval process failed. " . $e->getMessage());
    }
} elseif ($action == "reject") {
    // ✅ Reject the application
    $sql = "UPDATE student_room_applications SET status = 'Rejected' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    header("Location: admin_room_applications.php?message=Application Rejected");
    exit();
} else {
    die("Error: Invalid action.");
}
?>
