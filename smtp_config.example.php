<?php
// Copy this file to smtp_config.php and fill your SMTP credentials
// This is useful on local XAMPP/Windows where environment variables are not set.

define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', '587'); // 587 for TLS, 465 for SSL
define('SMTP_USER', 'your_smtp_username');
define('SMTP_PASS', 'your_smtp_password');
define('SMTP_FROM', 'no-reply@yourdomain.com');
define('SMTP_SECURE', 'tls'); // tls, ssl, or none
?>