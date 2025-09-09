<?php
session_start();
include('admin_dormitory_header.php'); // Include the existing header for the dormitory admin
include('config.php');

// Handle sorting
$valid_columns = ['id', 'full_name', 'room_name', 'approved_at'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $valid_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';
$reverse_order = ($order === 'ASC') ? 'DESC' : 'ASC';

// Handle search
$search = $_GET['search'] ?? '';

// Query for approved applicants
$sql = "SELECT sa.id, sa.user_id, sa.room_id, sa.applied_at AS approved_at, 
               TRIM(CONCAT(u.first_name, ' ', COALESCE(NULLIF(u.middle_name, ''), ''), ' ', u.last_name)) AS full_name, 
               r.name AS room_name
        FROM student_room_applications sa
        JOIN users u ON sa.user_id = u.user_id
        JOIN rooms r ON sa.room_id = r.id
        WHERE sa.status = 'Approved'
        AND (u.first_name LIKE ? OR u.last_name LIKE ? OR r.name LIKE ?)
        ORDER BY $sort $order";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('sss', $search_param, $search_param, $search_param);
$stmt->execute();
$query = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Applicants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th a {
            color: #3498db;
            text-decoration: none;
        }

        th a:hover {
            text-decoration: underline;
        }

        tbody tr:hover {
            background-color: #f4f4f4;
        }

        .action-btn {
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: #2980b9;
        }

        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .notification.success {
            background-color: #2ecc71;
            color: white;
        }

        .notification.error {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Approved Applicants</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by applicant name or room name" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Notification for success or error messages -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="notification <?php echo $_GET['msg'] == 'success' ? 'success' : 'error'; ?>">
                <?php echo $_GET['msg'] == 'success' ? 'Operation successful!' : 'Operation failed!'; ?>
            </div>
        <?php endif; ?>

        <!-- Approved Applicants Table -->
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=id&order=<?php echo $reverse_order; ?>">ID</a></th>
                    <th><a href="?sort=full_name&order=<?php echo $reverse_order; ?>">Applicant Name</a></th>
                    <th><a href="?sort=room_name&order=<?php echo $reverse_order; ?>">Room Name</a></th>
                    <th><a href="?sort=approved_at&order=<?php echo $reverse_order; ?>">Date Approved</a></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $query->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                        <td><?php echo $row['approved_at']; ?></td>
                        <td>
                            <a href="view_applicant.php?id=<?php echo $row['id']; ?>" class="action-btn">View</a>
                            <a href="edit_applicant.php?id=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
                            <a href="delete_applicant.php?id=<?php echo $row['id']; ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this applicant?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>