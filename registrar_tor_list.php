<?php
include 'config.php'; 
session_start();

// If form is submitted to update status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $tor_id = $_POST['tor_id'];
    $status = $_POST['status'];

    // Validate received data
    if (!empty($tor_id) && !empty($status)) {
        // Update query
        $updateQuery = "UPDATE requests SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $status, $tor_id);

        if ($stmt->execute()) {
            header("Location: registrar_tor_list.php?success=TOR request updated successfully");
            exit();
        } else {
            $error_message = "Update failed. Try again!";
        }
    } else {
        $error_message = "Invalid data. Please try again.";
    }
}

// If form is submitted to delete a request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_request'])) {
    $tor_id = $_POST['tor_id'];

    // Validate received data
    if (!empty($tor_id)) {
        // Delete query
        $deleteQuery = "DELETE FROM requests WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $tor_id);

        if ($stmt->execute()) {
            header("Location: registrar_tor_list.php?success=TOR request deleted successfully");
            exit();
        } else {
            $error_message = "Delete failed. Try again!";
        }
    } else {
        $error_message = "Invalid data. Please try again.";
    }
}

// Fetch all TOR requests
$query = "SELECT requests.*, users.first_name, users.last_name FROM requests JOIN users ON requests.student_id = users.user_id WHERE request_type = 'Transcript of Records'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOR Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            display: flex;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: #003366;
            color: white;
            position: fixed;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #002855;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #FFD700;
        }

        .sidebar-header i {
            margin-right: 10px;
            color: #FFD700;
        }

        .sidebar-menu a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 15px 20px;
            font-size: 1.1rem;
            margin: 8px 15px;
            background-color: #004080;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .sidebar-menu a:hover {
            background-color: #FFD700;
            color: #003366;
            transform: scale(1.05);
            font-weight: bold;
        }

        .logout-btn {
            text-decoration: none;
            color: white;
            display: block;
            padding: 15px;
            font-size: 1.1rem;
            background-color: #d9534f;
            margin: 30px 15px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c9302c;
            transform: scale(1.05);
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
            min-height: 100vh;
            background: white;  
        }

        h2 {
            text-align: center;
            color: #333333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #dddddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .action-link, .delete-link {
            color: white;
            text-decoration: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 4px;
            margin-right: 5px;
            display: inline-block;
        }
        .action-link {
            background-color: #007bff;
        }
        .action-link:hover {
            background-color: #0056b3;
        }
        .delete-link {
            background-color: #d9534f;
        }
        .delete-link:hover {
            background-color: #c9302c;
        }
        .update-form, .delete-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .update-form select, .update-form button, .delete-form button {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .update-form button, .delete-form button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .update-form button:hover, .delete-form button:hover {
            background-color: #0056b3;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa fa-user-shield"></i>
            <span>Admin Panel</span>
        </div>
        <div class="sidebar-menu">
            <a href="registrar_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="tor_list_admin.php"><i class="fas fa-file-alt"></i> TOR Requests</a>
    
        
        </div>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2>TOR Requests</h2>

        <?php if (isset($_GET['success'])): ?>
            <p class="success-message"><?= htmlspecialchars($_GET['success']) ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <span class="action-link" onclick="openUpdateModal('<?= htmlspecialchars($row['id']) ?>', '<?= htmlspecialchars($row['status']) ?>')">Update</span>
                            <span class="delete-link" onclick="openDeleteModal('<?= htmlspecialchars($row['id']) ?>')">Delete</span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Update Modal -->
        <div id="updateModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('updateModal')">&times;</span>
                <h2>Update TOR Request Status</h2>
                <form method="POST" class="update-form">
                    <input type="hidden" id="update_tor_id" name="tor_id">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                    <button type="submit" name="update_status">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
                <h2>Delete TOR Request</h2>
                <form method="POST" class="delete-form">
                    <input type="hidden" id="delete_tor_id" name="tor_id">
                    <p>Are you sure you want to delete this request?</p>
                    <button type="submit" name="delete_request">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUpdateModal(tor_id, currentStatus) {
            document.getElementById('update_tor_id').value = tor_id;
            document.getElementById('status').value = currentStatus;
            document.getElementById('updateModal').style.display = "flex";
        }

        function openDeleteModal(tor_id) {
            document.getElementById('delete_tor_id').value = tor_id;
            document.getElementById('deleteModal').style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('updateModal')) {
                closeModal('updateModal');
            } else if (event.target == document.getElementById('deleteModal')) {
                closeModal('deleteModal');
            }
        }
    </script>
</body>
</html>