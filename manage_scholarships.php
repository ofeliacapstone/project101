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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $eligibility = $_POST['eligibility'];
        $deadline = $_POST['deadline'];
        $status = 'active';

        $stmt = $conn->prepare("INSERT INTO scholarships (name, description, eligibility, deadline, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $description, $eligibility, $deadline, $status);
            if ($stmt->execute()) {
                echo "Scholarship added successfully!";
            } else {
                echo "Error adding scholarship: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $eligibility = $_POST['eligibility'];
        $deadline = $_POST['deadline'];

        $stmt = $conn->prepare("UPDATE scholarships SET name = ?, description = ?, eligibility = ?, deadline = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssssi", $name, $description, $eligibility, $deadline, $id);
            if ($stmt->execute()) {
                echo "Scholarship updated successfully!";
            } else {
                echo "Error updating scholarship: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Scholarship deleted successfully.";
                } else {
                    echo "Error: Scholarship not found or already deleted.";
                }
            } else {
                echo "Error deleting scholarship: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Invalid action.";
    }
}

// Fetch all scholarships
$sql = "SELECT * FROM scholarships ORDER BY deadline ASC";
$result = $conn->query($sql);

$conn->close();
?>