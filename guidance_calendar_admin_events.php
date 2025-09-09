<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) { http_response_code(403); exit; }
header('Content-Type: application/json; charset=utf-8');

$res = $conn->query("SELECT id, appointment_date, status, student_id FROM appointments WHERE appointment_date IS NOT NULL");
$acting_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
if ($role === 'Guidance Admin') {
    $res = $conn->query("SELECT id, appointment_date, status, student_id FROM appointments WHERE appointment_date IS NOT NULL");
} else {
    $stmt = $conn->prepare("SELECT id, appointment_date, status, student_id FROM appointments WHERE appointment_date IS NOT NULL AND user_id = ?");
    $stmt->bind_param('s', $acting_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
}
$events = [];
while ($r = $res->fetch_assoc()) {
    $status = strtolower($r['status']);
    // UI/UX: Improved colors and status icons
    if ($status === 'approved') {
        $color = '#33cc99';
        $icon  = '✅';
    } elseif ($status === 'pending') {
        $color = '#ffc107';
        $icon  = '⏳';
    } elseif ($status === 'completed') {
        $color = '#0d6efd';
        $icon  = '✔️';
    } else {
        $color = '#adb5bd';
        $icon  = '❌';
    }
    $title = $icon . ' Student #' . $r['student_id'] . ' (' . ucfirst($status) . ')';
    $startIso = date('c', strtotime($r['appointment_date']));
    $endIso = date('c', strtotime($r['appointment_date'] . ' +1 hour'));
    $events[] = [
        'id'    => $r['id'],
        'title' => $title,
        'start' => $startIso,
        'end'   => $endIso,
        'color' => $color
    ];
}
echo json_encode($events);