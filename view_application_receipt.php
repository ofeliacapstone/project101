<?php
session_start();
require_once 'config.php';
if (!isset($_GET['id'])) { echo 'Missing application ID'; exit(); }
$id = (int)$_GET['id'];
$sql = "SELECT sa.*, s.name AS scholarship_name, s.type AS scholarship_type, s.amount AS scholarship_amount,
        u.first_name, u.middle_name, u.last_name
        FROM scholarship_applications sa
        JOIN scholarships s ON s.id=sa.scholarship_id
        JOIN users u ON u.user_id=sa.user_id
        WHERE sa.id=?";
$st = $conn->prepare($sql);
$st->bind_param("i", $id);
$st->execute(); $app = $st->get_result()->fetch_assoc(); $st->close();
if (!$app) { echo 'Application not found'; exit(); }
$name = htmlspecialchars(trim(($app['first_name']??'').' '.(($app['middle_name']??'')?($app['middle_name'].' '):'').($app['last_name']??'')));
?>
<!DOCTYPE html>
<html><head>
<meta charset="UTF-8"><title>Application Receipt</title>
<link rel="stylesheet" href="assets/student_theme.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>.receipt{max-width:720px;margin:30px auto;padding:24px;border:1px solid #e5e5e5;border-radius:12px;background:#fff}</style>
<style>.receipt{max-width:720px;margin:30px auto;padding:24px;border:1px solid #e5e5e5;border-radius:12px;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.06)}</style>
<style>.receipt{max-width:720px;margin:30px auto;padding:24px;border:1px solid #e5e5e5;border-radius:12px;background:#fff}@media print{.receipt{box-shadow:none;border:0;margin:0;padding:0}button{display:none}}</style>
<script>function printPage(){window.print();}</script>
</head><body>
<div class="receipt">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">NEUST Gabaldon Scholarship Receipt</h5>
    <button class="btn btn-sm btn-outline-primary" onclick="printPage()">Print</button>
  </div><hr>
  <p><strong>Receipt No:</strong> <?= (int)$app['id'] ?></p>
  <p><strong>Student:</strong> <?= $name ?> (ID: <?= htmlspecialchars($app['user_id']) ?>)</p>
  <p><strong>Scholarship:</strong> <?= htmlspecialchars($app['scholarship_name']) ?> (<?= htmlspecialchars($app['scholarship_type']) ?>)</p>
  <p><strong>Applied On:</strong> <?= date('M j, Y g:i A', strtotime($app['application_date'])) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst(str_replace('_',' ',$app['status']))) ?></p>
  <?php if (!empty($app['review_notes'])): ?><p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($app['review_notes'])) ?></p><?php endif; ?>
  <hr><p class="text-muted mb-0">This is an auto-generated confirmation.</p>
</div>
</body></html>