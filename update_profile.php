<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

function clean($v) {
    return is_array($v) ? array_map('clean', $v) : htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please log in again.']);
    exit;
}
$user_id = $_SESSION['user_id'];

$photoOnly = (empty($_POST) && !empty($_FILES['profile_picture']['name']));

$fields = [
    'first_name', 'middle_name', 'last_name', 'birth_date', 'nationality', 'religion', 'biological_sex',
    'year', 'section', 'course', 'gpa',
    'mother_name', 'mother_work', 'mother_contact',
    'father_name', 'father_work', 'father_contact',
    'siblings_count', 'family_income',
    'email', 'phone', 'current_address', 'permanent_address'
];

$data = [];
foreach ($fields as $f) {
    $data[$f] = isset($_POST[$f]) ? clean($_POST[$f]) : null;
}

$profile_picture = null;
$avatar_url = null;

if (!empty($_FILES['profile_picture']['name'])) {
    $file = $_FILES['profile_picture'];
    $targetDir = "uploads/profile_pics/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $fileName = "user_" . $user_id . "_" . time() . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mimeType = mime_content_type($file['tmp_name']);
    $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($fileType, $allowedTypes) || !in_array($mimeType, $allowedMime)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image format.']);
        exit;
    }
    if ($file['size'] > 2*1024*1024) {
        echo json_encode(['status' => 'error', 'message' => 'Image size exceeds 2MB.']);
        exit;
    }
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
        exit;
    }
    $profile_picture = $targetFile;
    $avatar_url = $profile_picture;
}

if ($photoOnly) {
    if ($profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture=? WHERE user_id=?");
        $stmt->bind_param("ss", $profile_picture, $user_id);
        $ok = $stmt->execute();
        $stmt->close();
        if ($ok) {
            echo json_encode(['status' => 'success', 'message' => 'Profile photo updated.', 'profile_picture' => $avatar_url]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not update profile photo.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No image uploaded.']);
    }
    exit;
}

$required = [
    'first_name', 'last_name', 'birth_date', 'nationality', 'religion', 'biological_sex',
    'email', 'phone', 'current_address', 'permanent_address',
    'mother_name', 'mother_work', 'mother_contact',
    'father_name', 'father_work', 'father_contact',
    'siblings_count'
];
foreach ($required as $f) {
    if ($data[$f] === null || $data[$f] === "") {
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $f"]);
        exit;
    }
}

$set = [];
$vals = [];
foreach ($fields as $f) {
    if ($f == 'role') continue;
    if ($f == 'gpa' || $f == 'year' || $f == 'siblings_count' || $f == 'family_income') {
        $set[] = "$f=?";
        $vals[] = ($data[$f] !== '' && $data[$f] !== null) ? $data[$f] : null;
    } else {
        $set[] = "$f=?";
        $vals[] = $data[$f];
    }
}
if ($profile_picture) {
    $set[] = "profile_picture=?";
    $vals[] = $profile_picture;
}
$vals[] = $user_id;

$sql = "UPDATE users SET " . implode(",", $set) . " WHERE user_id=?";
$stmt = $conn->prepare($sql);
$types = str_repeat("s", count($vals));
$stmt->bind_param($types, ...$vals);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
}
?>