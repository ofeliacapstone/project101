<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { die('Not authenticated'); }
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title, status, submission_date, resolution_date FROM grievances WHERE user_id = ? ORDER BY submission_date DESC");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
        .container { max-width: 1100px; margin: 40px auto; background:#fff; border-radius:12px; box-shadow:0 10px 24px rgba(0,0,0,.06); padding:24px; }
        h2 { color:#002147; margin:0 0 16px }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:12px 10px; border-bottom:1px solid #eef2f6; text-align:left; }
        th { color:#334; font-weight:600; font-size:14px; }
        tr:hover { background:#fafbfd; }
        .badge { padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .b-pending { background:#fff3cd; color:#856404; }
        .b-resolved { background:#d4edda; color:#155724; }
        .b-rejected { background:#f8d7da; color:#721c24; }
        .link { color:#0b5ed7; text-decoration:none; }
        .link:hover { text-decoration:underline; }
        .actions { text-align:right }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
        .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#002147; color:#fff; text-decoration:none; font-weight:600; }
        .btn:hover { background:#0b3970; }
    </style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="container">
    <div class="topbar">
        <h2><i class="fa-solid fa-clipboard-list"></i> My Complaints</h2>
        <a class="btn" href="submit_grievance.php"><i class="fa-solid fa-plus"></i> New Complaint</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Resolved</th>
                <th class="actions">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
                <?php $status = strtolower($it['status']); $badge = 'b-' . $status; ?>
                <tr>
                    <td>#<?= (int)$it['id'] ?></td>
                    <td><?= htmlspecialchars($it['title']) ?></td>
                    <td><span class="badge <?= htmlspecialchars($badge) ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                    <td><?= htmlspecialchars(date('M d, Y H:i', strtotime($it['submission_date']))) ?></td>
                    <td><?= $it['resolution_date'] ? htmlspecialchars(date('M d, Y H:i', strtotime($it['resolution_date']))) : '—' ?></td>
                    <td class="actions"><a class="link" href="grievance_view.php?id=<?= (int)$it['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="6">No complaints yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>