<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dashboard statistics
$announcementCount = $conn->query("SELECT COUNT(*) AS total FROM announcements")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$grievanceCount = $conn->query("SELECT COUNT(*) AS total FROM grievances")->fetch_assoc()['total'];
$scholarshipCount = $conn->query("SELECT COUNT(*) AS total FROM scholarships")->fetch_assoc()['total'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Power Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="assets/admin_theme.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      display: flex;
    }
    .dashboard-container {
      display: flex;
      width: 100%;
    }
    .sidebar {
      width: 250px;
      background-color: #002147;
      color: white;
      height: 100vh;
      padding: 20px;
      position: fixed;
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
      position: relative;
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
    /* Dropdown styles */
    .dropdown > a::after {
      content: ' ▼';
      font-size: 0.8em;
    }
    .dropdown-content {
      display: none;
      background-color: #002147;
      padding-left: 15px;
      margin-top: 5px;
    }
    .dropdown-content a {
      display: block;
      padding: 8px 10px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
    }
    .dropdown-content a:hover {
      background-color: #FFD700;
      color: #002147;
      border-radius: 5px;
    }
    .dropdown.active .dropdown-content {
      display: block;
    }
    .main-content {
      margin-left: 270px;
      padding: 20px;
      width: calc(100% - 270px);
    }
    header {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    header { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
    .stats {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    .stat-card {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      flex: 1;
      min-width: 200px;
      text-align: center;
      box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
      text-decoration: none;
      color: black;
      transition: transform 0.2s;
    }
    .stat-card:hover {
      transform: scale(1.05);
    }
    .stat-card i {
      font-size: 30px;
      margin-bottom: 10px;
    }
    .chart-container {
      margin-top: 20px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
 
  <div class="dashboard-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="logo">NEUST Gabaldon</div>
      <ul class="nav-links">
        <li><a href="admin_dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="power_admin_announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li><a href="power_admin_users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#"><i class="fas fa-exclamation-triangle"></i> Grievances</a></li>
     
        <li class="dropdown">
          <a href="javascript:void(0);"><i class="fas fa-user-shield"></i> Manage Admin</a>
          <div class="dropdown-content">
            <a href="add_admin.php">Add Admin</a>
            <a href="admin_list.php">Admin List</a>
          </div>
        </li>
        <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
<?php include 'power_admin_header.php'; ?>
    <main class="main">
      <header>
        <h2>Power Admin Dashboard</h2>
      </header>

      <section class="stats">
        <a href="power_admin_announcement.php" class="stat-card">
          <i class="fas fa-bullhorn"></i>
          <p>Announcements</p>
          <span><?php echo $announcementCount; ?></span>
        </a>
        <a href="power_admin_users.php" class="stat-card">
          <i class="fas fa-users"></i>
          <p>Users</p>
          <span><?php echo $userCount; ?></span>
        </a>
        <a href="#" class="stat-card">
        <a href="power_admin_grievance_queue.php" class="stat-card">
          <i class="fas fa-exclamation-triangle"></i>
          <p>Grievances</p>
          <span><?php echo $grievanceCount; ?></span>
        </a>



      </section>

      <div class="chart-container">
        <canvas id="dashboardChart"></canvas>
      </div>
    </main>
  </div>

  <script>
    // Dropdown functionality
    document.querySelectorAll('.dropdown > a').forEach(dropdownLink => {
      dropdownLink.addEventListener('click', function() {
        this.parentElement.classList.toggle('active');
      });
    });
    
    // Chart.js for displaying dashboard stats
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Announcements', 'Users', 'Grievances', 'Scholarships'],
        datasets: [{
          label: 'System Stats',
          data: [<?php echo $announcementCount; ?>, <?php echo $userCount; ?>, <?php echo $grievanceCount; ?>, <?php echo $scholarshipCount; ?>],
          backgroundColor: ['#FFD700', '#002147', '#FF5733', '#1E90FF'],
        }]
      }
    });
  </script>
</body>
</html>