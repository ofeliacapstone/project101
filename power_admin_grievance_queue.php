<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') {
    header('Location: login.php');
    exit();
}
function db_table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count > 0;
}
function db_has_column(mysqli $conn, string $table, string $column): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count > 0;
}
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$severity = trim($_GET['severity'] ?? '');
$category_id = isset($_GET['category_id']) && ctype_digit((string)$_GET['category_id']) ? (int)$_GET['category_id'] : null;
$hasCategoryIdCol = db_has_column($conn, 'grievances', 'category_id');
$hasSeverityCol = db_has_column($conn, 'grievances', 'severity');
$hasAssignedCol = db_has_column($conn, 'grievances', 'assigned_to_user_id');
// Categories for filter
$categories = [];
if ($hasCategoryIdCol && db_table_exists($conn, 'grievance_categories')) {
    $res = $conn->query("SELECT id, name FROM grievance_categories ORDER BY name");
    if ($res) { $categories = $res->fetch_all(MYSQLI_ASSOC); }
}
// Build query
$sql = "SELECT g.id, g.title, g.status, g.submission_date, g.resolution_date, g.user_id" .
       ($hasSeverityCol ? ", g.severity" : "") .
       ($hasAssignedCol ? ", g.assigned_to_user_id" : "") .
       ($hasCategoryIdCol ? ", gc.name AS category_name" : ", g.category AS category_name") .
       " FROM grievances g " .
       ($hasCategoryIdCol ? "LEFT JOIN grievance_categories gc ON g.category_id = gc.id " : "");
$where = [];
$params = [];
$types = '';
if ($q !== '') {
    $where[] = "(g.title LIKE ? OR g.description LIKE ? OR g.user_id LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss';
}
if ($status !== '') {
    $where[] = "g.status = ?"; $params[] = $status; $types .= 's';
}
if ($hasSeverityCol && $severity !== '') {
    $where[] = "g.severity = ?"; $params[] = $severity; $types .= 's';
}
if ($hasCategoryIdCol && $category_id) {
    $where[] = "g.category_id = ?"; $params[] = $category_id; $types .= 'i';
}
if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' ORDER BY g.submission_date DESC LIMIT 200';
$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grievance Queue</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/admin_theme.css">
    <style>
        body { margin:0; }
        .layout { display:flex; }
        .sidebar { }
        .main { flex:1; }
        .filters { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
        .filters input, .filters select { }
        .right { text-align:right }
        .hint { color:#667; font-size:12px; margin-top:6px; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="logo" style="font-weight:700; font-size:18px; text-align:center; margin-bottom:12px">NEUST Gabaldon</div>
        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link active" href="#"><i class="fas fa-exclamation-triangle"></i> Grievance Queue</a>
        <a class="nav-link" href="power_admin_announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a>
        <a class="nav-link" href="power_admin_users.php"><i class="fas fa-users"></i> Users</a>
        <a class="nav-link" href="power_admin_reports.php"><i class="fas fa-chart-line"></i> Reports</a>
        <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>
<?php include 'power_admin_header.php'; ?>
    <main class="main">
        <div class="card">
            <h1><i class="fas fa-list"></i> Grievance Queue</h1>
            <form class="filters" method="get">
                <input class="input" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search title/desc/user">
                <select class="input" name="status">
                    <option value="">All Status</option>
                    <?php $statuses = ['pending','acknowledged','info_requested','assigned','in_progress','resolved','rejected','escalated','closed','reopened','withdrawn'];
                    foreach ($statuses as $s): ?>
                        <option value="<?= $s ?>"<?= $status===$s?' selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="input" name="severity" <?= $hasSeverityCol ? '' : 'disabled' ?>>
                    <option value="">All Severity</option>
                    <?php foreach (['low','medium','high','critical'] as $sev): ?>
                        <option value="<?= $sev ?>"<?= $severity===$sev?' selected':'' ?>><?= ucfirst($sev) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="input" name="category_id" <?= $hasCategoryIdCol ? '' : 'disabled' ?>>
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"<?= ($category_id===(int)$c['id'])?' selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn" type="submit">Filter</button>
                <a class="btn btn-ghost" href="power_admin_grievance_queue.php">Reset</a>
            </form>
            <div class="hint">Tip: For enhanced fields (severity, categories, assignment), run the migration at <code>migrate_grievances_web.php</code>.</div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Severity</th>
                        <th>Category</th>
                        <th>Submitted</th>
                        <th>Assigned</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): $st = strtolower($r['status']); ?>
                    <tr>
                        <td>#<?= (int)$r['id'] ?></td>
                        <td><?= htmlspecialchars($r['title']) ?></td>
                        <td><span class="badge b-<?= htmlspecialchars(str_replace(' ', '_', $st)) ?>"><?= htmlspecialchars(ucfirst($st)) ?></span></td>
                        <td><?= htmlspecialchars($r['severity'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['category_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars(date('M d, Y H:i', strtotime($r['submission_date']))) ?></td>
                        <td><?= htmlspecialchars($r['assigned_to_user_id'] ?? '—') ?></td>
                        <td class="right"><a class="btn" href="power_admin_grievance_workbench.php?id=<?= (int)$r['id'] ?>">Open</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="8">No grievances found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>