<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$host = "localhost"; 
$user = "root";
$password = "";
$dbname = "student_services_db";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $user, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error.");
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
if (!$student) {
    session_destroy();
    die("Student record not found.");
}
function getCount($conn, $table, $student_id, $request_type = null) {
    if ($request_type) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM requests WHERE student_id = ? AND request_type = ?");
        $stmt->bind_param("ss", $student_id, $request_type);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total'] ?? 0;
}
$appointments = getCount($conn, "appointments", $user_id);
$scholarships = getCount($conn, "requests", $user_id, "Scholarship");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Dashboard</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="assets/student_theme.css?ver=4">
</head>
<body>
	<?php include('student_header.php'); ?>
	<div class="container" style="max-width:980px;margin:0 auto;padding:28px 12px 38px 12px;">
		<div class="page-header">
			<h1 style="margin:0;font-size:2rem;font-weight:900;display:flex;align-items:center;gap:9px;">
			    <i class="fas fa-user-graduate"></i>
			    <span>Welcome, <?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>!</span>
			</h1>
			<p style="font-size:1rem;"><?php echo htmlspecialchars($student['user_id']); ?></p>
		</div>
		<section class="dashboard-grid">
			<a href="student_status_appointments.php" class="stat-card" aria-label="Appointments" tabindex="0">
				<i class="fas fa-calendar-check"></i>
				<p>Appointments</p>
				<span><?php echo htmlspecialchars(intval($appointments)); ?></span>
			</a>
			<a href="track_applications.php" class="stat-card" aria-label="Scholarships" tabindex="0">
				<i class="fas fa-graduation-cap"></i>
				<p>Scholarships</p>
				<span><?php echo htmlspecialchars(intval($scholarships)); ?></span>
			</a>
		</section>
	</div>
</body>
</html>