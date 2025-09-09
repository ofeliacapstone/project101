<?php
session_start();
include 'config.php';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You need to log in to view your application status.");
}
$user_id = $_SESSION['user_id'];
// Fetch the application status for the logged-in user
$query = "SELECT sa.id, sa.status, 
                 sa.approved_at,
                 TRIM(CONCAT(u.first_name, ' ', 
                 COALESCE(NULLIF(u.middle_name, ''), ''), ' ', 
                 u.last_name)) AS full_name, 
                 r.name AS room_name
          FROM student_room_applications sa
          JOIN users u ON sa.user_id = u.user_id
          JOIN rooms r ON sa.room_id = r.id
          WHERE sa.user_id = ?
          ORDER BY sa.applied_at DESC
          LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
// Handle the case when no application is found
if (!$application) {
    $application = [
        'full_name' => 'N/A',
        'room_name' => 'N/A',
        'status' => 'No application found',
        'approved_at' => null,
    ];
}
// Define badge class based on application status
$statusBadgeClass = match ($application['status']) {
    'Approved' => 'success',
    'Rejected' => 'danger',
    'Pending' => 'warning',
    default => 'secondary'
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Application Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #007bff;
        }

        .card-text {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .card-text strong {
            color: #333;
        }

        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .bg-Approved {
            background-color: #28a745;
            color: #fff;
        }

        .bg-Pending {
            background-color: #ffc107;
            color: #fff;
        }

        .bg-Rejected {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
    <?php include 'student_header.php'; // Include the header file ?>
<?php include('student_header.php'); ?>

<div class="container">
    <h2 class="text-center mb-4">Application Status</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-primary"><?= htmlspecialchars($application['full_name']) ?></h5>
            <p class="card-text"><strong>Room Applied:</strong> <?= htmlspecialchars($application['room_name']) ?></p>
            <p class="card-text"><strong>Status:</strong> <span class="badge bg-<?= $statusBadgeClass ?>">
                <?= htmlspecialchars($application['status']) ?>
            </span></p>
            <?php if ($application['status'] === 'Approved' && $application['approved_at']): ?>
            <p class="card-text"><strong>Approved At:</strong> <?= date("F j, Y, g:i a", strtotime($application['approved_at'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>