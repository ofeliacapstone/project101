<?php
// Your admin password (you can change this to whatever you want)
$password = "adminpassword"; // Example: adminpassword

// Generate the hashed password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo $hashed_password;
?>
