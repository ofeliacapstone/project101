<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
if (!isset($_SESSION['user_id'])) { echo "Error: User not logged in."; exit(); }
$user_id = $_SESSION['user_id'];
$sql = "SELECT sa.id, s.name, sa.application_date, sa.status, sa.approval_date
        FROM scholarship_applications sa
        JOIN scholarships s ON sa.scholarship_id=s.id
        WHERE sa.user_id=? ORDER BY sa.application_date DESC";
$sql = "SELECT sa.id, s.name, sa.application_date, sa.status, sa.approval_date FROM scholarship_applications sa JOIN scholarships s ON s.id=sa.scholarship_id WHERE sa.user_id=? ORDER BY sa.application_date DESC";
$st = $conn->prepare($sql);
$st->bind_param("s", $user_id);
$st->execute();
$res = $st->get_result();
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>My Applications</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/student_theme.css">
<style>
.badge-needsinfo { background:#cff4fc; color:#055160; }
.badge-withdrawn { background:#e2e3e5; color:#41464b; }
.table-responsive { background: var(--surface); border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.table thead { position: sticky; top:0; z-index:2; }
.tools { display:flex; gap:10px; margin-bottom:12px; }
</style>
</head>
<body>
<?php include('student_header.php'); ?>
<div class="container my-4">
  <div class="page-header"><h1>My Scholarship Applications</h1></div>
  <div class="tools">
    <input id="searchApps" class="form-control" placeholder="Search scholarship names..." style="max-width:320px">
    <select id="statusFilter" class="form-select" style="max-width:200px">
      <option value="">All statuses</option>
      <option value="approved">Approved</option>
      <option value="pending">Pending</option>
      <option value="rejected">Rejected</option>
      <option value="needs_info">Needs Info</option>
      <option value="withdrawn">Withdrawn</option>
    </select>
  </div>
  <div class="table-responsive bg-white rounded shadow-sm">
  <div class="table-responsive bg-white rounded shadow-sm" style="overflow: visible;">
    <table id="appsTable" class="table table-bordered mb-0 align-middle">
      <thead class="table-light"><tr><th>Scholarship</th><th>Applied</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr data-name="<?= strtolower($row['name']) ?>" data-status="<?= strtolower($row['status']) ?>">
          <td>
            <strong><?= htmlspecialchars($row['name']) ?></strong>
          </td>
          <td>
            <?= date('M d, Y', strtotime($row['application_date'])) ?>
          </td>
          <td>
            <?php
              $status = strtolower($row['status']);
              $badge = 'secondary';
              if ($status === 'approved') $badge = 'success';
              elseif ($status === 'rejected') $badge = 'danger';
              elseif ($status === 'pending') $badge = 'warning';
              elseif ($status === 'needs_info') $badge = 'info';
              elseif ($status === 'withdrawn') $badge = 'dark';
            ?>
            <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
          </td>
          <td>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#row<?= (int)$row['id'] ?>" aria-expanded="false" aria-controls="row<?= (int)$row['id'] ?>">
              Details
            </button>
          </td>
        </tr>
        <tr class="collapse" id="row<?= (int)$row['id'] ?>">
          <td colspan="4">
            <div class="text-muted small">Loading...</div>
            <div class="p-3">
              <div class="small text-muted">Application ID: <?= (int)$row['id'] ?></div>
              <div class="small">Track updates in this page. GPA is not considered for eligibility.</div>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/track_applications.js" defer></script>
</body>
</html>