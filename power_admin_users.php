<?php
include 'config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') { header('Location: login.php'); exit(); }

include 'config.php'; // Ensure this connects to your database properly
// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM users";
$totalQuery = "SELECT COUNT(*) as total FROM users";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRows = (int)$totalRow['total'];
$totalPages = max(1, (int)ceil($totalRows / $limit));

$query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .dashboard-container {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #002147;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links {
            list-style: none;
            padding: 0;
        }
        .nav-links li {
            margin: 15px 0;
        }
        .nav-links a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            transition: 0.3s;
        }
        .nav-links a:hover, .nav-links .active {
            background-color: #FFD700;
            color: #002147;
            border-radius: 5px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            gap: 15px;
        }
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .stat-card i {
            font-size: 30px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .activate-btn {
            background-color: #28a745;
            color: white;
        }
        .deactivate-btn {
            background-color: #dc3545;
            color: white;
        }
        .reset-btn {
            background-color: #ffc107;
            color: black;
        }
        button:hover {
            opacity: 0.8;
        }
    </style>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Power Admin · Users</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<link rel="stylesheet" href="assets/admin_theme.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo">NEUST Gabaldon</div>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php" ><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="power_admin_announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="power_admin_users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#><i class="fas fa-exclamation-triangle"></i> Grievances</a></li>

                <li>
                    <a href="#"><i class="fas fa-user-shield"></i> Manage Admin <i class="fas fa-caret-down"></i></a>
                    <ul style="list-style: none; padding-left: 20px; display: none;">
                        <li><a href="admin_list.php"><i class="fas fa-list"></i> Admin List</a></li>
                        <li><a href="add_admin.php"><i class="fas fa-user-plus"></i> Add Admin</a></li>
                    </ul>
                </li>
                <script>
                    // JavaScript to toggle dropdown visibility
                    document.querySelectorAll('.sidebar .nav-links li a').forEach(link => {
                        link.addEventListener('click', function(e) {
                            const dropdown = this.nextElementSibling;
                            if (dropdown && dropdown.tagName === 'UL') {
                                e.preventDefault();
                                dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
                            }
                        });
                    });
                </script>
                <li><a href="login.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2>User Management</h2>

            <?php
            // Pagination logic
            $limit = 6; // Number of rows per page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Get total number of rows
            $totalQuery = "SELECT COUNT(*) as total FROM users";
            $totalResult = $conn->query($totalQuery);
            $totalRow = $totalResult->fetch_assoc();
            $totalRows = $totalRow['total'];
            $totalPages = ceil($totalRows / $limit);

            // Fetch limited rows for the current page
            $query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
            $result = $conn->query($query);
            ?>

            <table>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Date Registered</th>
                    <th>Action</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile" width="40">
                            <?php else: ?>
                                <img src="default-profile.png" alt="Default Profile" width="40">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars(!empty($row['unit']) ? $row['unit'] : "Not Assigned"); ?></td>
                        <td style="color: <?php echo ($row['status'] == 'Active') ? 'green' : 'red'; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td><?php echo ($row['last_login']) ? $row['last_login'] : "Not Logged In Yet"; ?></td>
                        <td><?php echo $row['date_registered']; ?></td>
                        <td>
                            <!-- Activate / Deactivate Form -->
                            <form action="update_status.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="new_status" value="<?php echo ($row['status'] == 'Active') ? 'Inactive' : 'Active'; ?>">
                                <button type="submit" class="<?php echo ($row['status'] == 'Active') ? 'deactivate-btn' : 'activate-btn'; ?>">
                                    <?php echo ($row['status'] == 'Active') ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>

                            <!-- Reset Password Form -->
                            <form action="reset_password.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <button type="submit" class="reset-btn">Reset Password</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <!-- Pagination Links -->
            <div style="margin-top: 20px; text-align: center;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" style="margin-right: 10px;">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" style="margin-right: 10px; <?php echo ($i == $page) ? 'font-weight: bold;' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
<?php include 'power_admin_header.php'; ?>
	<main class="main">
		<div class="card">
			<h1><i class="fas fa-users"></i> Users</h1>
			<table>
				<tr>
					<th>Profile</th>
					<th>Name</th>
					<th>Email</th>
					<th>Role</th>
					<th>Phone</th>
					<th>Unit</th>
					<th>Status</th>
					<th>Last Login</th>
					<th>Date Registered</th>
					<th>Action</th>
				</tr>
				<?php while ($row = $result->fetch_assoc()): ?>
				<tr>
					<td>
						<?php if (!empty($row['profile_picture'])): ?>
							<img src="<?= htmlspecialchars($row['profile_picture']) ?>" alt="Profile" width="40">
						<?php else: ?>
							<img src="default-profile.png" alt="Default Profile" width="40">
						<?php endif; ?>
					</td>
					<td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
					<td><?= htmlspecialchars($row['email']) ?></td>
					<td><?= htmlspecialchars($row['role']) ?></td>
					<td><?= htmlspecialchars($row['phone']) ?></td>
					<td><?= htmlspecialchars(!empty($row['unit']) ? $row['unit'] : 'Not Assigned') ?></td>
					<td style="color: <?= ($row['status'] == 'Active') ? 'green' : 'red' ?>;">
						<?= htmlspecialchars($row['status']) ?>
					</td>
					<td><?= $row['last_login'] ? $row['last_login'] : 'Not Logged In Yet' ?></td>
					<td><?= $row['date_registered'] ?></td>
					<td>
						<form action="update_status.php" method="POST" style="display:inline;">
							<input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
							<input type="hidden" name="new_status" value="<?= ($row['status'] == 'Active') ? 'Inactive' : 'Active' ?>">
							<button type="submit" class="btn <?= ($row['status'] == 'Active') ? 'btn-ghost' : '' ?>">
								<?= ($row['status'] == 'Active') ? 'Deactivate' : 'Activate' ?>
							</button>
						</form>
						<form action="reset_password.php" method="POST" style="display:inline;">
							<input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
							<button type="submit" class="btn">Reset Password</button>
						</form>
					</td>
				</tr>
				<?php endwhile; ?>
			</table>
			<div style="margin-top: 16px; text-align:center;">
				<?php if ($page > 1): ?>
					<a class="btn btn-ghost" href="?page=<?= $page - 1 ?>">Previous</a>
				<?php endif; ?>
				<?php for ($i = 1; $i <= $totalPages; $i++): ?>
					<a class="btn <?= ($i == $page) ? '' : 'btn-ghost' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
				<?php endfor; ?>
				<?php if ($page < $totalPages): ?>
					<a class="btn btn-ghost" href="?page=<?= $page + 1 ?>">Next</a>
				<?php endif; ?>
			</div>
		</div>
	</main>
</body>
</html>