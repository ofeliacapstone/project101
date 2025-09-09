<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) {
	header("Location: login.php"); exit();
}
$ts = $conn->query("SELECT COUNT(*) c FROM scholarships")->fetch_assoc()['c'] ?? 0;
$tp = $conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$ta = $conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='approved'")->fetch_assoc()['c'] ?? 0;
$tr = $conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='rejected'")->fetch_assoc()['c'] ?? 0;
$tn = $conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='needs_info'")->fetch_assoc()['c'] ?? 0;
$tw = $conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='withdrawn'")->fetch_assoc()['c'] ?? 0;
$g = $conn->query("SELECT DATE_FORMAT(application_date,'%Y-%m') m, COUNT(*) c FROM scholarship_applications GROUP BY 1 ORDER BY 1");
$graph=[]; while($r=$g->fetch_assoc()) $graph[]=$r;
// Top scholarships by applications (limit 5)
$topQ = $conn->query("SELECT s.id, s.name, COUNT(sa.id) c FROM scholarship_applications sa JOIN scholarships s ON s.id=sa.scholarship_id GROUP BY s.id, s.name ORDER BY c DESC, s.name ASC LIMIT 5");
$topIds = []; $topNames = []; $topCounts = [];
while ($row=$topQ->fetch_assoc()) { $topIds[]=(int)$row['id']; $topNames[]=$row['name']; $topCounts[]=(int)$row['c']; }
// Recent applications activity (latest 8)
$recentQ = $conn->query("SELECT sa.id, s.name AS scholarship_name, sa.status, sa.application_date, u.first_name, u.last_name FROM scholarship_applications sa JOIN scholarships s ON s.id=sa.scholarship_id JOIN users u ON u.user_id=sa.user_id ORDER BY sa.application_date DESC LIMIT 8");
$recent = [];
while ($row=$recentQ->fetch_assoc()) { $recent[]=$row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Scholarship Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
:root {
	--bg: #f4f7fb;
	--text: #0b1729;
	--muted: #5b6a86;
	--card: #ffffff;
	--border: rgba(0,0,0,.08);
	--accent: #00509E;
	--accent-2: #FFD700;
}
body[data-theme="dark"] {
	--bg: radial-gradient(1200px 600px at 20% -10%, #0e1a30 0%, #071220 40%, #050c17 100%);
	--text: #e9eef7;
	--muted: #b9c6da;
	--card: rgba(255,255,255,.06);
	--border: rgba(255,255,255,.15);
	--accent: #0dcaf0;
	--accent-2: #ffd54f;
}
body { background: var(--bg); color: var(--text); }
.main-content { margin-left: 0; padding: 24px; }
@media (min-width: 992px){ .sidebar + .main-content{ margin-left:260px; } }
@media (min-width: 992px){ .main-content{ margin-left: 260px; } }
.header { background: linear-gradient(90deg,#00509E,#002855); color:#fff; padding:20px; border-radius:14px; margin-bottom:20px; box-shadow:0 8px 24px rgba(0,0,0,.25); }
.kpi-card { background: var(--card); border:1px solid var(--border); border-radius:14px; padding:18px; height:100%; backdrop-filter: blur(8px); }
.kpi-value { font-size: 2rem; font-weight: 800; color:var(--text); }
.kpi-label { color:var(--muted); }
.section-card { background: var(--card); border:1px solid var(--border); border-radius:14px; padding:16px; box-shadow:0 8px 24px rgba(0,0,0,.15); }
.badge-chip { border-radius: 999px; padding:6px 10px; font-size:.8rem; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-approved { background:#d4edda; color:#155724; }
.badge-rejected { background:#f8d7da; color:#721c24; }
.badge-needs { background:#cff4fc; color:#055160; }
.badge-withdrawn { background:#e2e3e5; color:#41464b; }
.table-darkglass { --bs-table-bg: transparent; color:var(--text); }
.table-darkglass tbody tr { border-bottom:1px solid var(--border); }
.table-darkglass td, .table-darkglass th { border-color: var(--border); }
.theme-toggle { background: transparent; border:1px solid rgba(255,255,255,.35); color:#fff; }
.quick-chips .btn { border-radius: 999px; }
</style>
</head>
<body data-theme="dark">
<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) include 'admin_scholarship_header.php'; ?>
<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) include_once 'admin_scholarship_header.php'; ?>
<div class="main-content">
  <div class="header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4 class="mb-1"><i class="fa fa-graduation-cap me-2"></i>Scholarship Admin Dashboard</h4>
      <div class="small text-white-50">Monitor applications, status trends, and take quick actions</div>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <div class="quick-chips d-none d-md-flex gap-2">
        <a class="btn btn-sm btn-light" href="manage_applications.php?filter_status=pending">Pending</a>
        <a class="btn btn-sm btn-warning" href="manage_applications.php?filter_status=needs_info">Needs Info</a>
        <a class="btn btn-sm btn-success" href="manage_applications.php?filter_status=approved">Approved</a>
      </div>
      <button id="themeToggle" class="btn btn-sm theme-toggle"><i class="fa fa-moon me-1"></i><span>Dark</span></button>
      <a href="admin_manage_scholarships.php" class="btn btn-warning"><i class="fa fa-cog me-1"></i>Manage Scholarships</a>
      <a href="manage_applications.php" class="btn btn-light"><i class="fa fa-list me-1"></i>Manage Applications</a>
    </div>
  </div>
  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="kpi-card h-100"><div class="d-flex align-items-center justify-content-between"><div class="kpi-value"><?= (int)$ts ?></div><i class="fa fa-award" style="font-size:1.6rem;color:var(--accent-2)"></i></div><div class="kpi-label">Total Scholarships</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card h-100"><div class="d-flex align-items-center justify-content-between"><div class="kpi-value"><?= (int)$tp ?></div><i class="fa fa-hourglass-half" style="font-size:1.6rem;color:#ffc107"></i></div><div class="kpi-label">Pending Applications</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card h-100"><div class="d-flex align-items-center justify-content-between"><div class="kpi-value"><?= (int)$ta ?></div><i class="fa fa-check-circle" style="font-size:1.6rem;color:#28a745"></i></div><div class="kpi-label">Approved</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card h-100"><div class="d-flex align-items-center justify-content-between"><div class="kpi-value"><?= (int)$tr ?></div><i class="fa fa-times-circle" style="font-size:1.6rem;color:#dc3545"></i></div><div class="kpi-label">Rejected</div></div></div>
  </div>
  <div class="row g-3 mb-3">
    <div class="col-lg-6">
      <div class="section-card">
        <div class="d-flex align-items-center justify-content-between mb-2"><h6 class="mb-0">Status Distribution</h6>
          <div class="d-flex gap-1">
            <span class="badge-chip badge-pending">Pending</span>
            <span class="badge-chip badge-approved">Approved</span>
            <span class="badge-chip badge-rejected">Rejected</span>
            <span class="badge-chip badge-needs">Needs Info</span>
            <span class="badge-chip badge-withdrawn">Withdrawn</span>
          </div>
        </div>
        <div id="statusDonut"></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="section-card">
        <div class="d-flex align-items-center justify-content-between mb-2"><h6 class="mb-0">Top Scholarships by Applications</h6></div>
        <div id="topBar"></div>
      </div>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="section-card">
        <h6 class="mb-2">Applications Over Time</h6>
        <div id="trendChart"></div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="section-card">
        <h6 class="mb-2">Recent Applications</h6>
        <div class="table-responsive">
          <table class="table table-darkglass table-hover mb-0">
            <thead><tr><th>Applicant</th><th>Scholarship</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if (!$recent): ?>
              <tr><td colspan="4" class="text-muted">No recent activity</td></tr>
            <?php else: foreach ($recent as $r): $st=strtolower($r['status']); ?>
              <tr>
                <td><?= htmlspecialchars(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?></td>
                <td><?= htmlspecialchars($r['scholarship_name'] ?? '') ?></td>
                <td>
                  <?php if ($st==='approved'): ?>
                    <span class="badge-chip badge-approved">Approved</span>
                  <?php elseif ($st==='rejected'): ?>
                    <span class="badge-chip badge-rejected">Rejected</span>
                  <?php elseif ($st==='needs_info'): ?>
                    <span class="badge-chip badge-needs">Needs Info</span>
                  <?php elseif ($st==='withdrawn'): ?>
                    <span class="badge-chip badge-withdrawn">Withdrawn</span>
                  <?php else: ?>
                    <span class="badge-chip badge-pending">Pending</span>
                  <?php endif; ?>
                </td>
                <td><?= date('M j, Y', strtotime($r['application_date'])) ?></td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  const body = document.body;
  const toggleBtn = document.getElementById('themeToggle');
  const saved = localStorage.getItem('theme') || 'dark';
  body.setAttribute('data-theme', saved);
  if (saved === 'light') { toggleBtn.innerHTML = '<i class="fa fa-moon me-1"></i><span>Dark</span>'; } else { toggleBtn.innerHTML = '<i class="fa fa-sun me-1"></i><span>Light</span>'; }
  toggleBtn.addEventListener('click', function(){
    const next = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    body.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    if (next === 'light') { toggleBtn.innerHTML = '<i class="fa fa-moon me-1"></i><span>Dark</span>'; } else { toggleBtn.innerHTML = '<i class="fa fa-sun me-1"></i><span>Light</span>'; }
  });
})();
const trendData = <?= json_encode($graph) ?>;
const trendLabels = trendData.map(d=>d.m);
const trendCounts = trendData.map(d=>parseInt(d.c||0));
new ApexCharts(document.querySelector("#trendChart"), {
  series:[{name:'Applications', data:trendCounts}],
  chart:{ type:'area', height:320, toolbar:{show:false}, zoom:{enabled:false}},
  dataLabels:{enabled:false}, stroke:{curve:'smooth', width:2},
  fill:{type:'gradient', gradient:{shadeIntensity:1, opacityFrom:.45, opacityTo:.05, stops:[0,90,100]}},
  xaxis:{ categories: trendLabels, labels:{style:{colors:'#8aa0b8'}} },
  yaxis:{ labels:{style:{colors:'#8aa0b8'}} },
  grid:{ borderColor:'rgba(128,128,128,.2)'}
}).render();
const statusLabels = ['pending','approved','rejected','needs_info','withdrawn'];
const statusSeries = [<?= (int)$tp ?>, <?= (int)$ta ?>, <?= (int)$tr ?>, <?= (int)$tn ?>, <?= (int)$tw ?>];
new ApexCharts(document.querySelector("#statusDonut"), {
  series: statusSeries,
  labels: ['Pending','Approved','Rejected','Needs Info','Withdrawn'],
  chart: { type:'donut', height:320, events:{
    dataPointSelection: function(e, ctx, config){
      const idx = config.dataPointIndex;
      const status = statusLabels[idx] || '';
      if (status) window.location = 'manage_applications.php?filter_status='+encodeURIComponent(status);
    }
  }},
  legend:{ position:'bottom', labels:{colors:'#8aa0b8'} },
  stroke:{ colors:['transparent'] },
  colors:['#ffc107','#28a745','#dc3545','#0dcaf0','#adb5bd'],
  dataLabels:{ enabled:true }
}).render();
const topIds = <?= json_encode($topIds) ?>;
const topNames = <?= json_encode($topNames) ?>;
const topCounts = <?= json_encode($topCounts) ?>;
new ApexCharts(document.querySelector("#topBar"), {
  series:[{ name:'Applications', data: topCounts }],
  chart:{ type:'bar', height:320, toolbar:{show:false}, events:{
    dataPointSelection: function(e, ctx, config){
      const idx = config.dataPointIndex;
      const sid = topIds[idx];
      if (sid) window.location = 'manage_applications.php?filter_scholarship='+encodeURIComponent(sid);
    }
  }},
  plotOptions:{ bar:{ horizontal:true, borderRadius:6 }},
  xaxis:{ categories: topNames, labels:{style:{colors:'#8aa0b8'}} },
  yaxis:{ labels:{style:{colors:'#8aa0b8'}} },
  colors:['var(--accent-2)'],
  grid:{ borderColor:'rgba(128,128,128,.2)'}
}).render();
</script>
</body>
</html>