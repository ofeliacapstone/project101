<?php
// Database connection (palitan mo ito ng tamang credentials mo)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_services_db"; // Palitan ng database name mo

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query para kunin ang users
$sql = "SELECT user_id, first_name, last_name, role FROM users";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($users);

$conn->close();
?>
