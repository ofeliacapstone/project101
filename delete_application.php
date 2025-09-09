<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) {
  http_response_code(403); echo 'Unauthorized'; exit();
}
if ($_SERVER['REQUEST_METHOD']!=='POST' || !isset($_POST['id'])) { echo 'Invalid request.'; exit(); }
$id = (int)$_POST['id'];

$st = $conn->prepare("DELETE FROM scholarship_applications WHERE id=?");
$st->bind_param("i", $id);
echo $st->execute() ? 'success' : 'error';
$st->close();