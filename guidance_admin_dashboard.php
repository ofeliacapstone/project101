<?php
include 'config.php';
session_start();
// Check if the user is logged in and is a guidance admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Guidance Admin') {
    header("Location: login.php");
    exit();
}
// Dashboard metrics
$totalPending = 0; $totalApprovedToday = 0; $upcomingCount = 0; $nextAppt = null; $upcomingRows = [];
try {
    $q1 = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status IN ('pending','Pending')");
    if ($q1) { $totalPending = (int)($q1->fetch_assoc()['c'] ?? 0); }
    $q2 = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status IN ('approved','Approved') AND DATE(appointment_date) = CURDATE()");
    if ($q2) { $totalApprovedToday = (int)($q2->fetch_assoc()['c'] ?? 0); }
    $q3 = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status IN ('approved','Approved') AND appointment_date >= NOW()");
    if ($q3) { $upcomingCount = (int)($q3->fetch_assoc()['c'] ?? 0); }
    $q4 = $conn->query("SELECT a.id, a.appointment_date, a.status, u.first_name, u.last_name FROM appointments a JOIN users u ON a.student_id=u.user_id WHERE a.appointment_date >= NOW() AND a.status IN ('approved','Approved') ORDER BY a.appointment_date ASC LIMIT 1");
    if ($q4) { $nextAppt = $q4->fetch_assoc(); }
    $q5 = $conn->query("SELECT a.id, a.appointment_date, a.status, u.first_name, u.last_name, a.reason FROM appointments a JOIN users u ON a.student_id=u.user_id WHERE a.appointment_date >= NOW() AND a.status IN ('approved','Approved','pending','Pending') ORDER BY a.appointment_date ASC LIMIT 6");
    if ($q5) { while($r=$q5->fetch_assoc()){ $upcomingRows[]=$r; } }
} catch (Throwable $e) { /* ignore metrics errors */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidance Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/guidance_theme.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        
        body { font-family:'Inter','Segoe UI',Arial,sans-serif; }
        .main-content { margin-left: 280px; padding: 24px; }
        .hero { background: linear-gradient(135deg, #003366 0%, #0b5ed7 100%); color:#fff; border-radius:16px; padding:24px; box-shadow:0 16px 40px rgba(2,32,71,.25); }
        .hero h2 { margin:0 0 8px; font-weight:800; letter-spacing:.3px; }
        .hero p { margin:0; opacity:.92; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:16px; margin-top:16px; }
        .stat { background:#fff; border-radius:14px; padding:18px; box-shadow:0 10px 30px rgba(2,32,71,.1); display:flex; align-items:center; gap:14px; }
        .stat .ic { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 8px 20px rgba(2,32,71,.12); }
        .ic-pending { background:#f59f00; }
        .ic-approved { background:#12b886; }
        .ic-upcoming { background:#0d6efd; }
        .ic-next { background:#845ef7; }
        .stat .txt { color:#0f172a; }
        .stat .txt .k { font-size:24px; font-weight:800; line-height:1; }
        .stat .txt .l { font-size:12px; color:#64748b; margin-top:2px; }
        .card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 10px 30px rgba(2,32,71,.1); }
        .card h3 { margin:0 0 12px; color:#0f172a; }
        .quick { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:12px; }
        .quick a { background:#0d6efd; color:#fff; text-decoration:none; padding:12px 14px; border-radius:12px; display:flex; align-items:center; gap:10px; box-shadow:0 8px 24px rgba(13,110,253,.22); font-weight:600; }
        .quick a.secondary { background:#12b886; box-shadow:0 8px 24px rgba(18,184,134,.22); }
        .quick a.warning { background:#f59f00; box-shadow:0 8px 24px rgba(245,159,0,.22); }
        table { width:100%; border-collapse:collapse; }
        thead th { position:sticky; top:0; background:#f8fafc; color:#334155; font-weight:600; padding:10px; border-bottom:1px solid #e5e7eb; }
        tbody td { padding:10px; border-bottom:1px solid #eef2f7; color:#0f172a; }
        .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .bg-pending { background:#fff3cd; color:#8a6d3b; }
        .bg-approved { background:#d1e7dd; color:#0f5132; }
    </style>
</head>
<body>
    <?php include 'guidance_admin_header.php'; ?>

    
    <div class="main-content guidance-theme">
        <div class="g-hero fade-in">
            <h2><i class="fa-solid fa-shield-heart"></i> Guidance Admin Dashboard</h2>
            <p>Oversee requests, appointments, announcements, and reports in one place.</p>
        </div>

        <div class="grid" style="margin-top:16px;">
           
            <div class="stat hover-rise">
                <div class="ic ic-pending"><i class="fa-solid fa-hourglass-half"></i></div>
                <div class="txt">
                    <div class="k"><?php echo htmlspecialchars((string)$totalPending); ?></div>
                    <div class="l">Pending Requests</div>
                </div>
            </div>
         
            <div class="stat hover-rise">
                <div class="ic ic-approved"><i class="fa-solid fa-check-circle"></i></div>
                <div class="txt">
                    <div class="k"><?php echo htmlspecialchars((string)$totalApprovedToday); ?></div>
                    <div class="l">Approved Today</div>
                </div>
            </div>
           
            <div class="stat hover-rise">
                <div class="ic ic-upcoming"><i class="fa-solid fa-calendar-day"></i></div>
                <div class="txt">
                    <div class="k"><?php echo htmlspecialchars((string)$upcomingCount); ?></div>
                    <div class="l">Upcoming Appts</div>
                </div>
            </div>
            
            <div class="stat hover-rise">
                <div class="ic ic-next"><i class="fa-solid fa-bell"></i></div>
                <div class="txt">
                    <div class="k"><?php echo $nextAppt ? htmlspecialchars(date('M d, H:i', strtotime($nextAppt['appointment_date']))) : '—'; ?></div>
                    <div class="l">Next Appointment</div>
                </div>
            </div>
        </div>

        <div class="grid" style="margin-top:16px;">
           
            <div class="card fade-in">
                <h3>Quick Actions</h3>
                <div class="quick">
                  
                    <a class="g-btn" href="guidance_list_admin.php"><i class="fa-solid fa-list"></i> Manage Requests</a>
                    <a class="g-btn" href="guidance_calendar_admin.php" style="background:var(--brand-2); box-shadow:0 8px 24px rgba(18,184,134,.22)"><i class="fa-solid fa-calendar"></i> Open Calendar</a>
                    <a class="g-btn" href="guidance_blackouts_admin.php" style="background:var(--warn); box-shadow:0 8px 24px rgba(245,159,0,.22)"><i class="fa-solid fa-cloud-slash"></i> Blackout Dates</a>
                    <a class="g-btn" href="generate_reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
                </div>
            </div>
            <div class="card">
            <div class="card fade-in">
                <h3>Upcoming Appointments</h3>
                <?php if (count($upcomingRows)): ?>
               
                <table class="g-table">
                    <thead><tr><th>When</th><th>Student</th><th>Status</th><th>Reason</th></tr></thead>
                    <tbody>
                        <?php foreach($upcomingRows as $r): $st=strtolower($r['status']); $cls=$st==='approved'?'bg-approved':'bg-pending'; ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($r['appointment_date']))); ?></td>
                            <td><?php echo htmlspecialchars(($r['first_name'] ?? '').' '.($r['last_name'] ?? '')); ?></td>
                            
                            <td><span class="g-badge <?php echo $st==='approved'?'approved':'pending'; ?>"><?php echo htmlspecialchars(ucfirst($st)); ?></span></td>
                            <td><?php echo htmlspecialchars($r['reason'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="color:#64748b;">No upcoming appointments.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>