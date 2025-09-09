<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { die('Not authenticated'); }
$user_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die('Invalid ID'); }

$stmt = $conn->prepare("SELECT g.*, c.name AS category_name FROM grievances g LEFT JOIN grievance_categories c ON g.category_id = c.id WHERE g.id = ? AND g.user_id = ?");
$stmt->bind_param('is', $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$grievance = $res ? $res->fetch_assoc() : null;
$stmt->close();
if (!$grievance) { die('Not found'); }

// Comments timeline (public only for user)
$comments = [];
$resC = @$conn->query("SELECT author_user_id, is_internal, body, created_at FROM grievance_comments WHERE grievance_id = " . (int)$id . " AND is_internal = 0 ORDER BY created_at ASC");
if ($resC) { $comments = $resC->fetch_all(MYSQLI_ASSOC); }

// Attachments
$attachments = [];
$resA = @$conn->query("SELECT file_path, file_type, file_size, created_at FROM grievance_attachments WHERE grievance_id = " . (int)$id . " ORDER BY created_at ASC");
if ($resA) { $attachments = $resA->fetch_all(MYSQLI_ASSOC); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint #<?= (int)$id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
        .wrap { max-width: 1100px; margin: 40px auto; display:grid; grid-template-columns: 2fr 1fr; gap:20px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 10px 24px rgba(0,0,0,.06); padding:20px; }
        .title { margin:0 0 6px; color:#0b3970 }
        .meta { color:#667; font-size: 14px; margin-bottom: 12px; }
        .badge { padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .timeline { list-style:none; padding:0; margin:0; }
        .timeline li { border-left:3px solid #e6eef9; margin-left:10px; padding-left:12px; padding-bottom:10px; }
        .attachments a { display:block; color:#0b5ed7; text-decoration:none; margin-bottom:6px; }
        .attachments a:hover { text-decoration:underline; }
        .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#002147; color:#fff; text-decoration:none; font-weight:600; }
        .btn:hover { background:#0b3970; }
    </style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="wrap">
    <div class="card">
        <h2 class="title">#<?= (int)$grievance['id'] ?> · <?= htmlspecialchars($grievance['title']) ?></h2>
        <div class="meta">
            Status: <span class="badge"><?= htmlspecialchars(ucfirst($grievance['status'])) ?></span>
            · Submitted: <?= htmlspecialchars(date('M d, Y H:i', strtotime($grievance['submission_date']))) ?>
            <?php if (!empty($grievance['resolution_date'])): ?>
                · Resolved: <?= htmlspecialchars(date('M d, Y H:i', strtotime($grievance['resolution_date']))) ?>
            <?php endif; ?>
        </div>
        <div style="white-space:pre-wrap; line-height:1.6; color:#223">
            <?= htmlspecialchars($grievance['description']) ?>
        </div>
    </div>
    <div class="card">
        <div class="grid">
            <div>
                <strong>Category</strong>
                <div><?= htmlspecialchars($grievance['category_name'] ?: ($grievance['category'] ?? '—')) ?></div>
            </div>
            <div>
                <strong>Severity</strong>
                <div><?= htmlspecialchars($grievance['severity'] ?? '—') ?></div>
            </div>
            <div>
                <strong>Anonymous</strong>
                <div><?= (isset($grievance['is_anonymous']) && (int)$grievance['is_anonymous'] === 1) ? 'Yes' : 'No' ?></div>
            </div>
            <div>
                <strong>Assigned To</strong>
                <div><?= htmlspecialchars($grievance['assigned_to_user_id'] ?? '—') ?></div>
            </div>
        </div>
        <?php if (!empty($attachments) || !empty($grievance['attachment'])): ?>
        <hr>
        <div class="attachments">
            <strong>Attachments</strong>
            <?php if (!empty($grievance['attachment'])): ?>
                <a href="<?= htmlspecialchars($grievance['attachment']) ?>" target="_blank">Attachment</a>
            <?php endif; ?>
            <?php foreach ($attachments as $a): ?>
                <a href="<?= htmlspecialchars($a['file_path']) ?>" target="_blank">File (<?= htmlspecialchars($a['file_type'] ?? 'file') ?>)</a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="card" style="grid-column: 1 / -1;">
        <h3 style="margin:0 0 10px; color:#002147"><i class="fa-solid fa-clock"></i> Timeline</h3>
        <ul class="timeline">
            <li>
                <strong>Submitted</strong>
                <div class="meta"><?= htmlspecialchars(date('M d, Y H:i', strtotime($grievance['submission_date']))) ?></div>
            </li>
            <?php foreach ($comments as $c): ?>
            <li>
                <strong>Reply from <?= htmlspecialchars($c['author_user_id']) ?></strong>
                <div style="white-space:pre-wrap; color:#223; margin:4px 0;"><?= htmlspecialchars($c['body']) ?></div>
                <div class="meta"><?= htmlspecialchars(date('M d, Y H:i', strtotime($c['created_at']))) ?></div>
            </li>
            <?php endforeach; ?>
            <?php if (!empty($grievance['resolution_date'])): ?>
            <li>
                <strong><?= htmlspecialchars(ucfirst($grievance['status'])) ?></strong>
                <div class="meta"><?= htmlspecialchars(date('M d, Y H:i', strtotime($grievance['resolution_date']))) ?></div>
            </li>
            <?php endif; ?>
        </ul>
        <div style="margin-top:10px">
            <a class="btn" href="grievance_list.php"><i class="fa-solid fa-arrow-left"></i> Back to My Complaints</a>
        </div>
    </div>
</div>
</body>
</html>