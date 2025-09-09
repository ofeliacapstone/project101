<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle actions (resolve, reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);

    if ($action === 'resolve') {
        $stmt = $conn->prepare("UPDATE grievances SET status = 'resolved', resolution_date = NOW() WHERE id = ?");
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE grievances SET status = 'rejected', resolution_date = NOW() WHERE id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Fetch user email to notify
        $userStmt = $conn->prepare("SELECT u.email FROM grievances g JOIN users u ON g.user_id = u.user_id WHERE g.id = ?");
        $userStmt->bind_param("i", $id);
        $userStmt->execute();
        $userStmt->bind_result($email);
        $userStmt->fetch();
        $userStmt->close();

        // Send notification email
        $subject = "Grievance Status Updated";
        $message = "Your grievance (ID: $id) has been " . ($action === 'resolve' ? 'resolved' : 'rejected') . ".";
        $headers = "From: no-reply@yourdomain.com";
        mail($email, $subject, $message, $headers);

        $stmt->close();
    }
}

// Fetch all grievances
$result = $conn->query("SELECT g.id, g.title, g.description, g.status, g.submission_date, g.resolution_date, u.first_name, u.last_name FROM grievances g JOIN users u ON g.user_id = u.user_id ORDER BY g.submission_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Grievances</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    

    <div class="container">
        <h2 class="text-center mt-4">Manage Grievances</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Submission Date</th>
                        <th>Resolution Date</th>
                        <th>Submitted By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($row['status'] == 'resolved' ? 'success' : ($row['status'] == 'rejected' ? 'danger' : 'warning')) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td><?= date("F j, Y, g:i A", strtotime($row['submission_date'])) ?></td>
                            <td><?= $row['resolution_date'] ? date("F j, Y, g:i A", strtotime($row['resolution_date'])) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <form method="POST" action="manage_grievances.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="resolve">
                                        <button type="submit" class="btn btn-success btn-sm">Resolve</button>
                                    </form>
                                    <form method="POST" action="manage_grievances.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>