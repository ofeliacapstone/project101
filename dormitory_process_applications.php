<?php
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = intval($_POST['application_id']);
    $action = $_POST['action'];

    // Debugging: Log received data
    error_log("Received request to update application ID $applicationId with action $action");

    // Fetch room_id for the application
    $stmt = $conn->prepare("SELECT room_id FROM student_room_applications WHERE id = ?");
    $stmt->bind_param('i', $applicationId);
    $stmt->execute();
    $stmt->bind_result($roomId);
    $stmt->fetch();
    $stmt->close();

    if ($roomId) {
        if ($action === 'approve') {
            $status = 'Approved';

            // Update occupied_beds and available_beds for the room
            $updateRoomStmt = $conn->prepare("UPDATE rooms SET occupied_beds = occupied_beds + 1 WHERE id = ?");
            $updateRoomStmt->bind_param('i', $roomId);
            if (!$updateRoomStmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Failed to update room occupancy.']);
                exit;
            }
            $updateRoomStmt->close();
        } elseif ($action === 'reject') {
            $status = 'Rejected';
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            exit;
        }

        // Update the application status
        $stmt = $conn->prepare("UPDATE student_room_applications SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $applicationId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Application has been updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update application.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Room not found for the application.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>