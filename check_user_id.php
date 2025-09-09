<?php
require 'config.php'; // Siguraduhin may koneksyon sa database

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $query = $conn->prepare("SELECT COUNT(*) FROM admins WHERE user_id = ?");
    $query->bind_param("s", $user_id);
    $query->execute();
    $query->bind_result($count);
    $query->fetch();
    
    echo ($count > 0) ? 'exists' : 'available';
}
?>
