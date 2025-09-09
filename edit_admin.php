<?php
include 'config.php'; 
session_start();


// Get admin ID from URL and validate
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $admin_id = intval($_GET['id']);

    // Fetch admin details
    $query = "SELECT * FROM admins WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    } else {
        header("Location: admin_list.php?error=Admin not found");
        exit();
    }
} else {
    header("Location: admin_list.php?error=Invalid Admin ID");
    exit();
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unit = $_POST['unit'];
    $role = $_POST['role'];

    // Update query
    $updateQuery = "UPDATE admins SET unit = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $unit, $role, $admin_id);

    if ($stmt->execute()) {
        header("Location: admin_list.php?success=Admin updated successfully");
        exit();
    } else {
        $error_message = "Update failed. Try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Edit Admin</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="unit">Unit:</label>
        <select name="unit" required>
            <option value="Guidance" <?= ($admin['unit'] == "Guidance") ? "selected" : "" ?>>Guidance</option>
            <option value="Dormitory" <?= ($admin['unit'] == "Dormitory") ? "selected" : "" ?>>Dormitory</option>
            <option value="Registrar" <?= ($admin['unit'] == "Registrar") ? "selected" : "" ?>>Registrar</option>
            <option value="Scholarship" <?= ($admin['unit'] == "Scholarship") ? "selected" : "" ?>>Scholarship</option>
        </select>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="Admin" <?= ($admin['role'] == "Admin") ? "selected" : "" ?>>Admin</option>
            <option value="Super Admin" <?= ($admin['role'] == "Super Admin") ? "selected" : "" ?>>Super Admin</option>
        </select>

        <button type="submit">Update Admin</button>
        <a href="admin_list.php">Cancel</a>
    </form>

</body>
</html>
