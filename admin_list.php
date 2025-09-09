<?php

// admin_list.php
require_once 'config.php'; // Database connection

// Fetch all admins (all fields)
$query = "SELECT * FROM users WHERE role NOT IN ('student', 'faculty')";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin List</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
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
    h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    .add-admin {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 15px;
      background-color: #002147;
      color: #FFD700;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.3s, color 0.3s;
    }
    .add-admin:hover {
      background-color: #FFD700;
      color: #002147;
    }
    .table-container {
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
      white-space: nowrap;
    }
    th {
      background-color: #002147;
      color: #FFD700;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .action-links a {
      margin: 0 5px;
      color: #002147;
    }
    .action-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="logo">NEUST Gabaldon</div>
      <ul class="nav-links">
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="power_admin_announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li><a href="power_admin_users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#"><i class="fas fa-exclamation-triangle"></i> Grievances</a></li>
       
        <li>
          <a href="#" class="active"><i class="fas fa-user-shield"></i> Manage Admin <i class="fas fa-caret-down"></i></a>
          <ul style="list-style: none; padding-left: 20px; display: none;">
            <li><a href="admin_list.php"><i class="fas fa-list"></i> Admin List</a></li>
            <li><a href="add_admin.php"><i class="fas fa-user-plus"></i> Add Admin</a></li>
          </ul>
        </li>
        <script>
          // JavaScript to toggle dropdown visibility
          document.querySelectorAll('.sidebar .nav-links li > a').forEach(link => {
            link.addEventListener('click', function(e) {
              const dropdown = this.nextElementSibling;
              if (dropdown && dropdown.tagName === 'UL') {
          e.preventDefault();
          dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
              }
            });
          });
        </script>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <h1>Admin List</h1>
      <a href="add_admin.php" class="add-admin">Add New Admin</a>
      
      <div class="table-container">
        <table id="adminTable">
          <thead>
            <tr>
              <th>User ID</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>Last Name</th>
              <th>Birth Date</th>
              <th>Nationality</th>
              <th>Religion</th>
              <th>Biological Sex</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Current Address</th>
              <th>Permanent Address</th>
              <th>Role</th>
              <th>Password Hash</th>
              <th>Mother Name</th>
              <th>Mother Work</th>
              <th>Mother Contact</th>
              <th>Father Name</th>
              <th>Father Work</th>
              <th>Father Contact</th>
              <th>Siblings Count</th>
              <th>Unit</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['birth_date']); ?></td>
                <td><?php echo htmlspecialchars($row['nationality']); ?></td>
                <td><?php echo htmlspecialchars($row['religion']); ?></td>
                <td><?php echo htmlspecialchars($row['biological_sex']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['current_address']); ?></td>
                <td><?php echo htmlspecialchars($row['permanent_address']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['password_hash']); ?></td>
                <td><?php echo htmlspecialchars($row['mother_name']); ?></td>
                <td><?php echo htmlspecialchars($row['mother_work']); ?></td>
                <td><?php echo htmlspecialchars($row['mother_contact']); ?></td>
                <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                <td><?php echo htmlspecialchars($row['father_work']); ?></td>
                <td><?php echo htmlspecialchars($row['father_contact']); ?></td>
                <td><?php echo htmlspecialchars($row['siblings_count']); ?></td>
                <td><?php echo htmlspecialchars($row['unit']); ?></td>
                <td class="action-links">
                  <a href="edit_admin.php?id=<?php echo urlencode($row['user_id']); ?>">Edit</a> |
                  <a href="delete_admin.php?id=<?php echo urlencode($row['user_id']); ?>" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#adminTable').DataTable({
        scrollX: true
      });
    });
  </script>
</body>
</html>
