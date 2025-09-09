<?php
include 'config.php'; 
session_start();





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card h3 {
            margin-bottom: 10px;
            color: #333333;
        }
        .card p {
            font-size: 1.2rem;
            color: #555555;
        }
    </style>
</head>
<body>
    <?php include 'registrar_admin_header.php'; ?>
    <div class="main-content">
        <h2>Dashboard</h2>
        <div class="card">
            <h3>Total TOR Requests</h3>
        
        </div>
        <div class="card">
            <h3>Total Students</h3>
        
        </div>
        <div class="card">
            <h3>Total Courses</h3>
       
        </div>
    </div>
</body>
</html>