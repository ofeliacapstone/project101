<?php
include 'config.php'; 
session_start();



// Fetch user profile data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        .profile-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 400px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #333333;
        }
        .profile-field {
            margin-bottom: 15px;
        }
        .profile-field label {
            font-weight: bold;
            color: #555555;
        }
        .profile-field p {
            font-size: 1.1rem;
            color: #333333;
        }
    </style>
</head>
<body>
    <?php include 'registrar_admin_header.php'; ?>
    <div class="main-content">
        <div class="profile-container">
            <h2>Profile</h2>
            <div class="profile-field">
                <label>First Name:</label>
                <p><?= htmlspecialchars($user['first_name']) ?></p>
            </div>
            <div class="profile-field">
                <label>Last Name:</label>
                <p><?= htmlspecialchars($user['last_name']) ?></p>
            </div>
            <div class="profile-field">
                <label>Email:</label>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div class="profile-field">
                <label>Phone:</label>
                <p><?= htmlspecialchars($user['phone']) ?></p>
            </div>
            <div class="profile-field">
                <label>Role:</label>
                <p><?= htmlspecialchars($user['role']) ?></p>
            </div>
        </div>
    </div>
</body>
</html>