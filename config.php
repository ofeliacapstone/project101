<?php
$host = "localhost"; // Change if your database is hosted elsewhere
$user = "root"; // Default user for XAMPP
$pass = ""; // Default password (leave empty if not set)
$dbname = "student_services_db"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for better character encoding
$conn->set_charset("utf8mb4");

// Uncomment to check if the connection is successful
// echo "Connected successfully";
?>
