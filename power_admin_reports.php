<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') {
    header('Location: login.php');
    exit();
}
// Basic metrics
$total = (int)($conn->query("SELECT COUNT(*) c FROM grievances")->fetch_assoc()['c'] ?? 0);
$resolved = (int)($conn->query("SELECT COUNT(*) c FROM grievances WHERE status='resolved'")->fetch_assoc()['c'] ?? 0);
$pending = (int)($conn->query("SELECT COUNT(*) c FROM grievances WHERE status='pending'")->fetch_assoc()['c'] ?? 0);
$rejected = (int)($conn->query("SELECT COUNT(*) c FROM grievances WHERE status='rejected'")->fetch_assoc()['c'] ?? 0);
// Counts by status
$byStatus = [];
$res = $conn->query("SELECT status, COUNT(*) c FROM grievances GROUP BY status");
if ($res) { while ($r = $res->fetch_assoc()) { $byStatus[$r['status']] = (int)$r['c']; } }
// Counts by month (last 6)
$byMonth = [];
$res = $conn->query("SELECT DATE_FORMAT(submission_date,'%Y-%m') ym, COUNT(*) c FROM grievances GROUP BY ym ORDER BY ym DESC LIMIT 6");
if ($res) { while ($r = $res->fetch_assoc()) { $byMonth[$r['ym']] = (int)$r['c']; } }
$byMonth = array_reverse($byMonth, true);
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="grievance_report.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','User','Title','Status','Submitted','Resolved']);
    $rs = $conn->query("SELECT id, user_id, title, status, submission_date, resolution_date FROM grievances ORDER BY submission_date DESC");
    while ($row = $rs->fetch_assoc()) {
        fputcsv($out, [$row['id'],$row['user_id'],$row['title'],$row['status'],$row['submission_date'],$row['resolution_date']]);
    }
    fclose($out);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grievance Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/admin_theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="layout" style="display:flex;">
    <aside class="sidebar">
        <div class="logo" style="font-weight:700; font-size:18px; text-align:center; margin-bottom:12px">NEUST Gabaldon</div>
        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link" href="power_admin_grievance_queue.php"><i class="fas fa-list"></i> Grievance Queue</a>
        <a class="nav-link active" href="#"><i class="fas fa-chart-line"></i> Reports</a>
        <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>
<?php include 'power_admin_header.php'; ?>
    <main class="main">
        <div class="card">
            <h1>Grievance Reports</h1>
            <div class="grid grid-3">
                <div class="kpi"><span class="num"><?= $total ?></span> <span class="lbl">Total</span></div>
                <div class="kpi"><span class="num"><?= $pending ?></span> <span class="lbl">Pending</span></div>
                <div class="kpi"><span class="num"><?= $resolved ?></span> <span class="lbl">Resolved</span></div>
            </div>
        </div>
        <div class="grid grid-2" style="margin-top:16px;">
            <div class="card">
                <h3>By Status</h3>
                <canvas id="byStatus"></canvas>
            </div>
            <div class="card">
                <h3>Submissions (Last 6 months)</h3>
                <canvas id="byMonth"></canvas>
            </div>
        </div>
        <div class="card" style="margin-top:16px;">
            <a class="btn" href="power_admin_reports.php?export=csv"><i class="fa-solid fa-file-export"></i> Export CSV</a>
        </div>
    </main>
</div>
<script>
const statusData = <?= json_encode($byStatus) ?>;
const byStatusCtx = document.getElementById('byStatus');
new Chart(byStatusCtx, {
  type: 'doughnut',
  data: { labels: Object.keys(statusData), datasets: [{ data: Object.values(statusData), backgroundColor: ['#fbbf24','#60a5fa','#34d399','#f87171','#a78bfa','#f472b6'] }] },
  options: { plugins: { legend: { position: 'bottom' }}}
});
const monthData = <?= json_encode($byMonth) ?>;
const byMonthCtx = document.getElementById('byMonth');
new Chart(byMonthCtx, {
  type: 'bar',
  data: { labels: Object.keys(monthData), datasets: [{ data: Object.values(monthData), label: 'Submissions', backgroundColor: '#60a5fa' }] },
  options: { plugins: { legend: { display:false }}, scales: { y: { beginAtZero:true }}}
});
</script>
</body>
</html>