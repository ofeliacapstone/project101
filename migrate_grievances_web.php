<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Grievance Migration</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 40px; }
        .card { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); padding: 24px; }
        h1 { margin: 0 0 10px; color: #002147; }
        p { color: #445; }
        pre { background: #0b1a33; color: #e6edf3; padding: 16px; border-radius: 8px; overflow:auto }
        .btn { display:inline-block; padding:10px 16px; border-radius:8px; background:#002147; color:#fff; text-decoration:none; }
        .btn:hover { background:#0b3970; }
    </style>
    </head>
<body>
    <div class="card">
        <h1>Grievance Migration</h1>
        <p>This will create/update tables for the complete grievance lifecycle.</p>
        <pre>
<?php
require_once __DIR__ . '/migrate_grievances.php';
?>
        </pre>
        <a class="btn" href="student_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>