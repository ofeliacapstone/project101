<?php
session_start();
include 'admin_dormitory_header.php'; // Include the header for the dormitory admin
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all pending applications
$query = "SELECT sa.id, sa.user_id, sa.room_id, sa.status, 
                 TRIM(CONCAT(u.first_name, ' ', COALESCE(NULLIF(u.middle_name, ''), ''), ' ', u.last_name)) AS full_name, 
                 r.name AS room_name
          FROM student_room_applications sa
          JOIN users u ON sa.user_id = u.user_id
          JOIN rooms r ON sa.room_id = r.id
          WHERE sa.status = 'Pending'
          ORDER BY sa.applied_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Pending Room Applications</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Room Applied</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['room_name']) ?></td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>
                        <button class="btn btn-success approve-btn" data-id="<?= intval($row['id']) ?>">Approve</button>
                        <button class="btn btn-danger reject-btn" data-id="<?= intval($row['id']) ?>">Reject</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".approve-btn, .reject-btn").forEach(button => {
        button.addEventListener("click", function () {
            const applicationId = this.getAttribute("data-id");
            const action = this.classList.contains("approve-btn") ? "approve" : "reject";

            if (confirm(`Are you sure you want to ${action} this application?`)) {
                fetch("dormitory_process_applications.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ application_id: applicationId, action: action })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while processing the request.");
                });
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>