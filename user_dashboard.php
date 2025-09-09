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

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Fetch user grievances
$result = $conn->query("SELECT * FROM grievances WHERE user_id = '$user_id' ORDER BY submission_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background-color: #003366;
            color: white;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            color: white;
        }

        .badge-success {
            background-color: green;
        }

        .badge-danger {
            background-color: red;
        }

        .badge-warning {
            background-color: orange;
        }
    </style>
</head>
<body>

<div class="container">
