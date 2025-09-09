<?php
require 'config.php';
require_once 'mailer.php';

$id = (int)($_GET['id'] ?? 0);
$s = $_GET['s'] ?? '';
$e = $_GET['e'] ?? '';
$sig = $_GET['sig'] ?? '';

if ($id <= 0 || !$s || !$e || !$sig) { http_response_code(400); exit('Bad request'); }

// Validate signature
$expected = ics_hmac($id, $s, $e);
if (!hash_equals($expected, $sig)) { http_response_code(403); exit('Forbidden'); }

// Fetch appointment details
$stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.status, a.reason, s.email AS s_email, TRIM(CONCAT(s.first_name,' ',s.last_name)) AS s_name, TRIM(CONCAT(c.first_name,' ',c.last_name)) AS c_name FROM appointments a JOIN users s ON a.student_id=s.user_id JOIN users c ON a.user_id=c.user_id WHERE a.id=?");
$stmt->bind_param('i', $id);
$stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
if (!$row) { http_response_code(404); exit('Not found'); }

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=appointment_'.$id.'.ics');

$uid = $id.'@neust-guidance';
$summary = 'Guidance Appointment - '.$row['s_name'].' with '.$row['c_name'];
$desc = 'Status: '.$row['status']."\nReason: ".($row['reason'] ?? '');
echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//NEUST Guidance//EN\r\n";
echo "BEGIN:VEVENT\r\n";
echo "UID:$uid\r\n";
echo "DTSTAMP:".gmdate('Ymd\THis\Z')."\r\n";
echo "DTSTART:$s\r\n";
echo "DTEND:$e\r\n";
echo "SUMMARY:".str_replace(["\n","\r"],' ',$summary)."\r\n";
echo "DESCRIPTION:".str_replace(["\n","\r"],' ', $desc)."\r\n";
echo "END:VEVENT\r\n";
echo "END:VCALENDAR\r\n";
?>
