<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Content-Type: application/json'); echo json_encode(['error'=>'User not authenticated']); exit(); }

if (!isset($_GET['id'])) { header('Content-Type: application/json'); echo json_encode(['error'=>'No scholarship ID provided']); exit(); }
$id = (int)$_GET['id'];

$sql = "SELECT s.*,
        (SELECT COUNT(*) FROM scholarship_applications sa WHERE sa.scholarship_id=s.id) AS current_applicants,
        (SELECT COUNT(*) FROM scholarship_applications sa WHERE sa.scholarship_id=s.id AND sa.status='approved') AS approved_applicants
        FROM scholarships s WHERE s.id=? AND s.status='active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) { header('Content-Type: application/json'); echo json_encode(['error'=>'Scholarship not found or inactive']); exit(); }
$stmt->close();

// user application state
$user_id = $_SESSION['user_id'];
$chk = $conn->prepare("SELECT status FROM scholarship_applications WHERE scholarship_id=? AND user_id=?");
$chk->bind_param("is", $id, $user_id);
$chk->execute();
$app = $chk->get_result()->fetch_assoc();
$chk->close();
if ($app) { $row['user_application_status']=$app['status']; $row['can_apply']=false; $row['application_message']='You have already applied for this scholarship.'; }
else { $row['user_application_status']=null; $row['can_apply']=true; $row['application_message']='You can apply for this scholarship.'; }

// deadline flag
// Deadline comparison is inclusive of the deadline date and ignores invalid dates
if (!empty($row['deadline']) && $row['deadline'] !== '0000-00-00') {
    $dt = DateTime::createFromFormat('Y-m-d', $row['deadline']);
    $isValid = $dt && $dt->format('Y-m-d') === $row['deadline'];
    if ($isValid) {
        $deadlineDate = $dt->format('Y-m-d');
        $todayDate = date('Y-m-d');
        $row['deadline_passed'] = ($deadlineDate < $todayDate);
        if ($row['deadline_passed']) { $row['can_apply']=false; $row['application_message']='Application deadline has passed.'; }
        $row['deadline_iso'] = $deadlineDate;
        $row['deadline_display'] = date('M j, Y', strtotime($deadlineDate));
    } else {
        $row['deadline_passed'] = false;
        $row['deadline_iso'] = null;
        $row['deadline_display'] = null;
    }
} else {
    $row['deadline_passed'] = false;
    $row['deadline_iso'] = null;
    $row['deadline_display'] = null;
}

// max applicants
if ((int)$row['max_applicants'] > 0 && (int)$row['current_applicants'] >= (int)$row['max_applicants']) {
    $row['can_apply']=false; $row['application_message']='Maximum number of applicants reached.';
}

// documents list
if (!empty($row['documents_required'])) {
    $docs = json_decode($row['documents_required'], true);
    $row['documents_list'] = is_array($docs) ? $docs : [$row['documents_required']];
} else $row['documents_list']=[];

$row['days_until_deadline'] = null;
if (!empty($row['deadline']) && $row['deadline'] !== '0000-00-00') {
    $dt = DateTime::createFromFormat('Y-m-d', $row['deadline']);
    if ($dt && $dt->format('Y-m-d') === $row['deadline']) {
        $deadlineMidnight = strtotime($dt->format('Y-m-d 23:59:59'));
        $now = time();
        $row['days_until_deadline'] = max(0, ceil(($deadlineMidnight - $now) / 86400));
    }
}
$row['amount_formatted'] = number_format((float)$row['amount'],2);

// user info snapshot
$us = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$us->bind_param("s", $user_id);
$us->execute();
$ud = $us->get_result()->fetch_assoc();
$us->close();
if ($ud) {
    $row['user_eligibility'] = [
        'course' => $ud['course'] ?? null,
        'year_level' => $ud['year'] ?? null,
        'family_income' => $ud['family_income'] ?? null
    ];
}

header('Content-Type: application/json');
echo json_encode($row);