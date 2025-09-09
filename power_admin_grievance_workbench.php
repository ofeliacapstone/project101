<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') {
    header('Location: login.php');
    exit();
}
$admin_id = $_SESSION['user_id'] ?? 'admin01';
function db_has_column(mysqli $conn, string $table, string $column): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute(); $stmt->bind_result($c); $stmt->fetch(); $stmt->close();
    return (int)$c > 0;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die('Invalid ID'); }
// Actions: acknowledge, assign, request-info, resolve, reject, escalate, reopen, close
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $reason = trim($_POST['reason'] ?? '');
    $assign_to = trim($_POST['assign_to'] ?? '');
    $hasAssignedCol = db_has_column($conn, 'grievances', 'assigned_to_user_id');
    $hasResSummary = db_has_column($conn, 'grievances', 'resolution_summary');
    $hasAckAt = db_has_column($conn, 'grievances', 'acknowledged_at');
    $hasFirstResp = db_has_column($conn, 'grievances', 'first_response_at');
    $hasInProgressAt = db_has_column($conn, 'grievances', 'in_progress_at');
    $hasClosedAt = db_has_column($conn, 'grievances', 'closed_at');
    if ($action === 'acknowledge') {
        $conn->query("UPDATE grievances SET status='acknowledged'" . ($hasAckAt?", acknowledged_at=NOW()":"") . ($hasFirstResp?", first_response_at=NOW()":"") . " WHERE id=" . (int)$id);
    } elseif ($action === 'assign' && $hasAssignedCol && $assign_to !== '') {
        $stmt = $conn->prepare("UPDATE grievances SET status='assigned', assigned_to_user_id=?" . ($hasInProgressAt?", in_progress_at=NOW()":"") . " WHERE id=?");
        $stmt->bind_param('si', $assign_to, $id); $stmt->execute(); $stmt->close();
    } elseif ($action === 'request-info') {
        // record as public comment and set status info_requested
        $body = $reason !== '' ? $reason : 'Please provide additional information.';
        $insC = $conn->prepare("INSERT INTO grievance_comments (grievance_id, author_user_id, is_internal, body) VALUES (?, ?, 0, ?)");
        $insC->bind_param('iss', $id, $admin_id, $body); @$insC->execute(); $insC->close();
        $conn->query("UPDATE grievances SET status='info_requested' WHERE id=" . (int)$id);
    } elseif ($action === 'resolve') {
        if ($hasResSummary) {
            $stmt = $conn->prepare("UPDATE grievances SET status='resolved', resolution_summary=?, resolution_date=NOW() WHERE id=?");
            $stmt->bind_param('si', $reason, $id); $stmt->execute(); $stmt->close();
        } else {
            $conn->query("UPDATE grievances SET status='resolved', resolution_date=NOW() WHERE id=" . (int)$id);
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE grievances SET status='rejected', rejection_reason=?, resolution_date=NOW() WHERE id=?");
        $stmt->bind_param('si', $reason, $id); $stmt->execute(); $stmt->close();
    } elseif ($action === 'escalate') {
        $conn->query("UPDATE grievances SET status='escalated' WHERE id=" . (int)$id);
    } elseif ($action === 'reopen') {
        $conn->query("UPDATE grievances SET status='reopened' WHERE id=" . (int)$id);
    } elseif ($action === 'close') {
        $conn->query("UPDATE grievances SET status='closed'" . ($hasClosedAt?", closed_at=NOW()":"") . " WHERE id=" . (int)$id);
    }
    header('Location: power_admin_grievance_workbench.php?id=' . $id);
    exit();
}
$hasSeverityCol = db_has_column($conn, 'grievances', 'severity');
$hasAssignedCol = db_has_column($conn, 'grievances', 'assigned_to_user_id');
$stmt = $conn->prepare("SELECT g.*, u.first_name, u.last_name FROM grievances g JOIN users u ON g.user_id = u.user_id WHERE g.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$g = $res ? $res->fetch_assoc() : null;
$stmt->close();
if (!$g) { die('Not found'); }
$comments = [];
$resC = @$conn->query("SELECT author_user_id, is_internal, body, created_at FROM grievance_comments WHERE grievance_id = " . (int)$id . " ORDER BY created_at ASC");
if ($resC) { $comments = $resC->fetch_all(MYSQLI_ASSOC); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workbench #<?= (int)$id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/admin_theme.css">
    <style>
        body { margin:0; }
        .layout { display:flex; }
        .sidebar { }
        .main { flex:1; }
        .row { display:flex; gap:12px; }
        .col { flex:1; }
        textarea, input, select { }
        .right { text-align:right }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="logo" style="font-weight:700; font-size:18px; text-align:center; margin-bottom:12px">NEUST Gabaldon</div>
        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link" href="power_admin_grievance_queue.php"><i class="fas fa-list"></i> Grievance Queue</a>
        <a class="nav-link active" href="#"><i class="fas fa-tools"></i> Workbench</a>
        <a class="nav-link" href="power_admin_reports.php"><i class="fas fa-chart-line"></i> Reports</a>
        <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>
<?php include 'power_admin_header.php'; ?>
    <main class="main">
        <div class="card">
            <h1>#<?= (int)$g['id'] ?> · <?= htmlspecialchars($g['title']) ?></h1>
            <div class="muted">By <?= htmlspecialchars($g['first_name'] . ' ' . $g['last_name']) ?> · Status: <span class="badge b-<?= htmlspecialchars(str_replace(' ', '_', strtolower($g['status']))) ?>"><?= htmlspecialchars(ucfirst($g['status'])) ?></span></div>
            <div style="white-space:pre-wrap; line-height:1.6; color:#223; margin-bottom:8px;"><?= htmlspecialchars($g['description']) ?></div>
            <form method="post" class="row">
                <div class="col">
                    <label>Action</label>
                    <select class="input" name="action">
                        <option value="acknowledge">Acknowledge</option>
                        <option value="assign" <?= $hasAssignedCol? '':'disabled' ?>>Assign</option>
                        <option value="request-info">Request Info</option>
                        <option value="resolve">Resolve</option>
                        <option value="reject">Reject</option>
                        <option value="escalate">Escalate</option>
                        <option value="close">Close</option>
                    </select>
                </div>
                <div class="col">
                    <label>Assign To (User ID)</label>
                    <input class="input" name="assign_to" placeholder="e.g., Guidance01" <?= $hasAssignedCol? '':'disabled' ?> />
                </div>
                <div class="col">
                    <label>Notes / Reason</label>
                    <textarea class="input" name="reason" rows="2" placeholder="Optional notes or resolution summary"></textarea>
                </div>
            </form>
            <div class="right" style="margin-top:10px">
                <button formmethod="post" formaction="power_admin_grievance_workbench.php?id=<?= (int)$id ?>" class="btn">Apply</button>
            </div>
        </div>
        <div class="card" style="margin-top:16px;">
            <h3 style="margin:0 0 8px;">Timeline</h3>
            <ul class="timeline" style="list-style:none; padding:0; margin:0">
                <li style="border-left:3px solid #e6eef9; margin-left:10px; padding-left:12px; padding-bottom:10px;">
                    <strong>Submitted</strong>
                    <div class="muted"><?= htmlspecialchars(date('M d, Y H:i', strtotime($g['submission_date']))) ?></div>
                </li>
                <?php foreach ($comments as $c): ?>
                    <li style="border-left:3px solid #e6eef9; margin-left:10px; padding-left:12px; padding-bottom:10px;">
                        <strong><?= $c['is_internal'] ? 'Internal Note' : 'Public Reply' ?> by <?= htmlspecialchars($c['author_user_id']) ?></strong>
                        <div style="white-space:pre-wrap; color:#223; margin:4px 0;">&quot;<?= htmlspecialchars($c['body']) ?>&quot;</div>
                        <div class="muted"><?= htmlspecialchars(date('M d, Y H:i', strtotime($c['created_at']))) ?></div>
                    </li>
                <?php endforeach; ?>
                <?php if (!empty($g['resolution_date'])): ?>
                    <li style="border-left:3px solid #e6eef9; margin-left:10px; padding-left:12px; padding-bottom:10px;">
                        <strong><?= htmlspecialchars(ucfirst($g['status'])) ?></strong>
                        <div class="muted"><?= htmlspecialchars(date('M d, Y H:i', strtotime($g['resolution_date']))) ?></div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </main>
</div>
</body>
</html>