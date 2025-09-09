<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { http_response_code(403); echo 'Unauthorized'; exit(); }

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=applications_export.csv');

// Filters align with manage_applications
$where = [];
if (!empty($_GET['filter_scholarship'])) { $sid = (int)$_GET['filter_scholarship']; $where[] = "sa.scholarship_id=".$sid; }
if (!empty($_GET['filter_status'])) { $st = $conn->real_escape_string($_GET['filter_status']); $where[] = "sa.status='".$st."'"; }
if (!empty($_GET['from'])) { $from = $conn->real_escape_string($_GET['from']); $where[] = "DATE(sa.application_date) >= '".$from."'"; }
if (!empty($_GET['to'])) { $to = $conn->real_escape_string($_GET['to']); $where[] = "DATE(sa.application_date) <= '".$to."'"; }
$whereSql = $where ? ('WHERE '.implode(' AND ',$where)) : '';

$sql = "SELECT sa.id, sa.user_id, s.name AS scholarship_name, sa.status, sa.application_date, sa.approval_date, u.first_name, u.middle_name, u.last_name, u.email, u.course, u.`year` AS year_level
        FROM scholarship_applications sa
        JOIN scholarships s ON sa.scholarship_id = s.id
        JOIN users u ON sa.user_id = u.user_id
        $whereSql
        ORDER BY sa.application_date DESC";
$res = $conn->query($sql);

$out = fopen('php://output', 'w');
fputcsv($out, ['Application ID','User ID','Scholarship','Status','Applied At','Approved At','First Name','Middle Name','Last Name','Email','Course','Year']);
if ($res) {
	while ($row = $res->fetch_assoc()) {
		fputcsv($out, [
			$row['id'],
			$row['user_id'],
			$row['scholarship_name'],
			$row['status'],
			$row['application_date'],
			$row['approval_date'],
			$row['first_name'],
			$row['middle_name'],
			$row['last_name'],
			$row['email'],
			$row['course'],
			$row['year_level'],
		]);
	}
}
fclose($out);