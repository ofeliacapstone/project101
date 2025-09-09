<?php
include 'config.php'; // Ensure this file exists and is correctly set up

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ Get form data safely
    $user_id = trim($_POST['user_id'] ?? ""); // Power Admin's input
    $first_name = trim($_POST['first_name'] ?? "");
    $middle_name = trim($_POST['middle_name'] ?? "");
    $last_name = trim($_POST['last_name'] ?? "");
    $birth_date = trim($_POST['birth_date'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $phone = trim($_POST['phone'] ?? "");
    $current_address = trim($_POST['current_address'] ?? "");
    $permanent_address = trim($_POST['permanent_address'] ?? "");
    $role = isset($_POST['role']) ? trim($_POST['role']) : ""; 
    $password = $_POST['password'] ?? "";
    $mother_name = trim($_POST['mother_name'] ?? "");
    $mother_work = trim($_POST['mother_work'] ?? "");
    $mother_contact = trim($_POST['mother_contact'] ?? "");
    $father_name = trim($_POST['father_name'] ?? "");
    $father_work = trim($_POST['father_work'] ?? "");
    $father_contact = trim($_POST['father_contact'] ?? "");
    $siblings_count = $_POST['siblings_count'] ?? "0";
    $unit = trim($_POST['unit'] ?? "");

    // ✅ If role is empty, use unit as the role
    if (empty($role) && !empty($unit)) {
        $role = $unit; // Assign unit as role
    }

    // ✅ Validation checks
    if (empty($user_id)) {
        echo "<script>alert('Error: User ID is required.'); window.history.back();</script>";
        exit();
    }
    if (empty($role)) {
        echo "<script>alert('Error: Role is required.'); window.history.back();</script>";
        exit();
    }
    if ($role == "Dormitory Admin" && empty($unit)) {
        echo "<script>alert('Error: Unit is required for Dormitory Admin.'); window.history.back();</script>";
        exit();
    }
    if (strlen($password) < 8) {
        echo "<script>alert('Error: Password must be at least 8 characters long.'); window.history.back();</script>";
        exit();
    }
    
    // ✅ Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Check if `user_id` or `email` already exists
    $checkQuery = "SELECT user_id, email FROM users WHERE email = ? OR user_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $email, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Error: Email or User ID already exists.'); window.history.back();</script>";
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();

    // ✅ Prepare SQL Query
    $sql = "INSERT INTO users 
            (user_id, first_name, middle_name, last_name, birth_date, email, phone, current_address, permanent_address, role, password_hash, mother_name, mother_work, mother_contact, father_name, father_work, father_contact, siblings_count, unit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // ✅ Bind Parameters
        $stmt->bind_param("sssssssssssssssssss", $user_id, $first_name, $middle_name, $last_name, $birth_date, $email, $phone, $current_address, $permanent_address, $role, $hashed_password, $mother_name, $mother_work, $mother_contact, $father_name, $father_work, $father_contact, $siblings_count, $unit);

        // ✅ Execute
        if ($stmt->execute()) {
            echo "<script>alert('User added successfully! User ID: $user_id'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: " . $conn->error . "'); window.history.back();</script>";
    }
    
    // ✅ Close Connection
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>