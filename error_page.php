<?php
$statusCode = http_response_code();
$messages = [
    400 => 'Invalid request. Please check the provided data and try again.',
    403 => 'You do not have permission to access this resource.',
    404 => 'The requested resource was not found.',
    429 => 'Too many requests. Please wait a moment and try again.',
    500 => 'An unexpected error occurred. Please try again or contact the system administrator.'
];
$message = $messages[$statusCode] ?? 'An error occurred.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | Dormitory Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="card p-4 text-center fade-in">
        <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
        <h2>Error <?php echo $statusCode; ?></h2>
        <p class="text-muted"><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php" class="btn btn-primary">Back to Payments</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>