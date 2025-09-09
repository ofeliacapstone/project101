<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
// Restrict to Scholarship Admin or Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin', 'Admin'])) {
	header("Location: login.php");
	exit();
}
// Bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'], $_POST['ids'])) {
	header('Content-Type: application/json');
	if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Invalid CSRF token']); exit(); }
	$bulk = $_POST['bulk_action'];
	$ids = array_filter(array_map('intval', (array)$_POST['ids']));
	if (!$ids) { echo json_encode(['status'=>'error','message'=>'No items selected']); exit(); }
	$reviewedBy = $_SESSION['user_id'];
	try {
		if ($bulk === 'approve') {
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='approved', approval_date=NOW(), rejection_reason=NULL, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			foreach ($ids as $id) { $stmt->bind_param("si", $reviewedBy, $id); $stmt->execute(); }
			$stmt->close();
			echo json_encode(['status'=>'success']); exit();
		} elseif ($bulk === 'reject') {
			$reason = trim($_POST['rejection_reason'] ?? '');
			if ($reason==='') { echo json_encode(['status'=>'error','message'=>'Rejection reason is required']); exit(); }
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='rejected', approval_date=NULL, rejection_reason=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			foreach ($ids as $id) { $stmt->bind_param("ssi", $reason, $reviewedBy, $id); $stmt->execute(); }
			$stmt->close();
			echo json_encode(['status'=>'success']); exit();
		} elseif ($bulk === 'pending') {
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='pending', approval_date=NULL, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			foreach ($ids as $id) { $stmt->bind_param("si", $reviewedBy, $id); $stmt->execute(); }
			$stmt->close();
			echo json_encode(['status'=>'success']); exit();
		}
		echo json_encode(['status'=>'error','message'=>'Invalid bulk action']); exit();
	} catch (Exception $e) { echo json_encode(['status'=>'error','message'=>$e->getMessage()]); exit(); }
}
// Handle single Approve/Reject/Pending (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['application_id'])) {
	header('Content-Type: application/json');
	if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Invalid CSRF token']); exit(); }
	$action = $_POST['action'];
	$applicationId = (int)$_POST['application_id'];
	try {
		// Fetch application owner and scholarship for notifications/audit context
		$appStmt = $conn->prepare("SELECT sa.user_id, s.name AS scholarship_name FROM scholarship_applications sa JOIN scholarships s ON s.id = sa.scholarship_id WHERE sa.id = ?");
		$appStmt->bind_param("i", $applicationId);
		$appStmt->execute();
		$appInfo = $appStmt->get_result()->fetch_assoc();
		$appStmt->close();
		if (!$appInfo) throw new Exception('Application not found.');
		if ($action === 'approve') {
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='approved', approval_date=NOW(), rejection_reason=NULL, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			$reviewedBy = $_SESSION['user_id'];
			$stmt->bind_param("si", $reviewedBy, $applicationId);
			$ok = $stmt->execute();
			$stmt->close();
			if (!$ok) throw new Exception("Approve failed.");
			// Notify student
			$ns = $conn->prepare("INSERT INTO scholarship_notifications (user_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'success', 0, NOW())");
			$title = 'Application Approved';
			$message = 'Your application for '.($appInfo['scholarship_name'] ?? 'the scholarship').' has been approved.';
			$ns->bind_param("sss", $appInfo['user_id'], $title, $message);
			$ns->execute();
			$ns->close();
			echo json_encode(['status'=>'success','message'=>'Application approved']);
			exit();
		} elseif ($action === 'reject') {
			$reason = trim($_POST['rejection_reason'] ?? '');
			if ($reason === '') throw new Exception('Rejection reason is required.');
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='rejected', approval_date=NULL, rejection_reason=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			$reviewedBy = $_SESSION['user_id'];
			$stmt->bind_param("ssi", $reason, $reviewedBy, $applicationId);
			$ok = $stmt->execute();
			$stmt->close();
			if (!$ok) throw new Exception("Reject failed.");
			// Notify student
			$ns = $conn->prepare("INSERT INTO scholarship_notifications (user_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'error', 0, NOW())");
			$title = 'Application Rejected';
			$message = 'Your application for '.($appInfo['scholarship_name'] ?? 'the scholarship').' was rejected. Reason: '.$reason;
			$ns->bind_param("sss", $appInfo['user_id'], $title, $message);
			$ns->execute();
			$ns->close();
			echo json_encode(['status'=>'success','message'=>'Application rejected']);
			exit();
		} elseif ($action === 'set_pending') {
			$stmt = $conn->prepare("UPDATE scholarship_applications SET status='pending', approval_date=NULL, reviewed_by=?, reviewed_at=NOW() WHERE id=?");
			$reviewedBy = $_SESSION['user_id'];
			$stmt->bind_param("si", $reviewedBy, $applicationId);
			$ok = $stmt->execute();
			$stmt->close();
			if (!$ok) throw new Exception("Status update failed.");
			echo json_encode(['status'=>'success','message'=>'Application set to pending']);
			exit();
		}
		throw new Exception('Invalid action.');
	} catch (Exception $e) {
		echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
		exit();
	}
}
// Stats
$totals = ['total'=>0,'pending'=>0,'approved'=>0,'rejected'=>0];
if ($r=$conn->query("SELECT COUNT(*) c FROM scholarship_applications")) $totals['total']=(int)$r->fetch_assoc()['c'];
if ($r=$conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='pending'")) $totals['pending']=(int)$r->fetch_assoc()['c'];
if ($r=$conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='approved'")) $totals['approved']=(int)$r->fetch_assoc()['c'];
if ($r=$conn->query("SELECT COUNT(*) c FROM scholarship_applications WHERE status='rejected'")) $totals['rejected']=(int)$r->fetch_assoc()['c'];
// Filters
$scholarshipsList = [];
$qq = $conn->query("SELECT id, name FROM scholarships ORDER BY name ASC");
while ($rr = $qq->fetch_assoc()) $scholarshipsList[] = $rr;
$qq->close();
// Fetch applications with optional filters
$where = [];
if (!empty($_GET['filter_scholarship'])) { $sid = (int)$_GET['filter_scholarship']; $where[] = "sa.scholarship_id=".$sid; }
if (!empty($_GET['filter_status'])) { $st = $conn->real_escape_string($_GET['filter_status']); $where[] = "sa.status='".$st."'"; }
if (!empty($_GET['from'])) { $from = $conn->real_escape_string($_GET['from']); $where[] = "DATE(sa.application_date) >= '".$from."'"; }
if (!empty($_GET['to'])) { $to = $conn->real_escape_string($_GET['to']); $where[] = "DATE(sa.application_date) <= '".$to."'"; }
$whereSql = $where ? ('WHERE '.implode(' AND ',$where)) : '';
// Pagination
$perPage = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;
// Total count for pagination
$countSql = "SELECT COUNT(*) c FROM scholarship_applications sa JOIN scholarships s ON sa.scholarship_id=s.id JOIN users u ON sa.user_id=u.user_id ";
if ($whereSql) { $countSql .= $whereSql; }
$totalCount = 0; if ($rc = $conn->query($countSql)) { $totalCount = (int)($rc->fetch_assoc()['c'] ?? 0); }
$totalPages = (int)ceil($totalCount / $perPage);
$sql = "SELECT sa.id, sa.scholarship_id, sa.user_id, sa.application_date, sa.status, sa.approval_date, s.name AS scholarship_name, s.type AS scholarship_type, s.amount AS scholarship_amount, u.first_name, u.middle_name, u.last_name, u.email, u.phone, u.course, u.`year` AS year_level, u.section FROM scholarship_applications sa JOIN scholarships s ON sa.scholarship_id = s.id JOIN users u ON sa.user_id = u.user_id ";
if ($whereSql) { $sql .= $whereSql; }
$sql .= " ORDER BY sa.application_date DESC LIMIT ".$offset.", ".$perPage;
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Scholarship Applications</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
:root { --blue:#003366; --light:#00509E; --gold:#FFD700; --gray:#F8F9FA; --sidebar-width:260px; }
body { background: var(--gray); }
/* Prevent overlap with fixed admin sidebar/header */
.main-content { margin-left: var(--sidebar-width); padding: 20px; }
@media (max-width: 991.98px){ .main-content{ margin-left:0; } }
.main-content { margin-left: var(--sidebar-width); padding: 20px; padding-bottom: 96px; }
@media (max-width: 991.98px){ .main-content{ margin-left:0; padding-bottom: 96px; } }
.header { background: linear-gradient(135deg,var(--blue),var(--light)); color:#fff; padding:16px; border-radius:10px; margin:20px 0; }
.stats-card { background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.06); padding:20px; text-align:center; height:100%; }
.stats-number { font-size:1.8rem; color:var(--blue); font-weight:700; }
.controls .form-control, .controls .form-select, .controls .btn { min-height: 42px; }
.card-app { background:#fff; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,.08); margin-bottom:16px; border:0; overflow:hidden; }
.card-app .card-header{ background:linear-gradient(135deg,var(--blue),var(--light)); color:#fff; }
.badge-status{ border-radius:20px; padding:6px 12px; text-transform:uppercase; font-size:.8rem; }
.badge-pending{ background:#fff3cd; color:#856404; }
.badge-approved{ background:#d4edda; color:#155724; }
.badge-rejected{ background:#f8d7da; color:#721c24; }
.btn-primary{ background:var(--blue); border:none; }
.btn-primary:hover{ background:var(--light); }
.btn-info{ background:#17a2b8; border:none; }
.sticky-actions{ position: sticky; bottom: 0; background: #fff; padding: 8px; border-top: 1px solid #eee; }
.sticky-actions{ position: sticky; bottom: 0; background: #fff; padding: 8px 12px; border-top: 1px solid #eee; z-index: 1030; box-shadow: 0 -4px 12px rgba(0,0,0,.05); }
.container-fluid { overflow-x: hidden; }
.btn-group .btn, .d-flex.gap-2 .btn { white-space: nowrap; }
</style>
</head>
<body>
<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) include 'admin_scholarship_header.php'; ?>
<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) include_once 'admin_scholarship_header.php'; ?>
<div class="main-content">
  <div class="container-fluid">
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="header d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h4 class="mb-0"><i class="fa fa-file-alt me-2"></i> Manage Applications</h4>
      <div class="d-flex gap-2">
        <a href="scholarship_admin_dashboard.php" class="btn btn-sm btn-light">Dashboard</a>
        <a href="admin_manage_scholarships.php" class="btn btn-sm btn-light">Scholarships</a>
        <a href="approved_scholars.php" class="btn btn-sm btn-light">Approved Scholars</a>
        <a href="export_applications_csv.php?<?= htmlspecialchars(http_build_query($_GET)) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-file-csv"></i> Export CSV</a>
      </div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-sm-6 col-lg-3"><div class="stats-card"><div class="stats-number"><?= $totals['total'] ?></div><div>Total Applications</div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stats-card"><div class="stats-number"><?= $totals['pending'] ?></div><div>Pending</div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stats-card"><div class="stats-number"><?= $totals['approved'] ?></div><div>Approved</div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stats-card"><div class="stats-number"><?= $totals['rejected'] ?></div><div>Rejected</div></div></div>
    </div>
    <div class="row g-2 mb-3 controls align-items-end">
      <div class="col-12 col-md-3">
        <label class="form-label">Search</label>
        <input id="searchInput" type="text" class="form-control" placeholder="Search by student or scholarship..." />
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Status</label>
        <select id="statusFilter" class="form-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Scholarship</label>
        <select id="scholarshipFilter" class="form-select">
          <option value="">All Scholarships</option>
          <?php foreach ($scholarshipsList as $s): ?>
            <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Date Range</label>
        <div class="d-flex gap-2">
          <input id="dateFrom" type="date" class="form-control" />
          <input id="dateTo" type="date" class="form-control" />
        </div>
      </div>
      <div class="col-12 mt-2">
        <button id="applyFilters" class="btn btn-outline-primary">Apply Filters</button>
        <button id="resetFilters" class="btn btn-outline-secondary">Reset</button>
      </div>
    </div>
    <div class="sticky-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
      <div>
        <input type="checkbox" id="selectAll"> <label for="selectAll" class="ms-1">Select All</label>
      </div>
      <div class="d-flex gap-2">
        <button id="bulkApprove" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Approve Selected</button>
        <button id="bulkPending" class="btn btn-primary btn-sm"><i class="fa fa-rotate-left"></i> Set Pending</button>
        <button id="bulkReject" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Reject Selected</button>
      </div>
    </div>
    <div id="applicationsGrid">
      <?php if ($result && $result->num_rows > 0): while ($row = $result->fetch_assoc()):
        $fullName = trim(($row['first_name'] ?? '').' '.(($row['middle_name'] ?? '') ? $row['middle_name'].' ' : '').($row['last_name'] ?? ''));
        $status = strtolower($row['status'] ?? 'pending');
      ?>
      <div class="card card-app application-item" data-name="<?= strtolower(htmlspecialchars($fullName.' '.$row['scholarship_name'])) ?>" data-status="<?= htmlspecialchars($status) ?>" data-scholarship-id="<?= (int)$row['scholarship_id'] ?>" data-applied="<?= date('Y-m-d', strtotime($row['application_date'])) ?>">
        <div class="card-header">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="form-check">
              <input class="form-check-input select-app" type="checkbox" value="<?= (int)$row['id'] ?>" id="sel<?= (int)$row['id'] ?>">
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-0"><?= htmlspecialchars($row['scholarship_name'] ?? '') ?></h6>
              <small><?= htmlspecialchars($row['scholarship_type'] ?? '') ?> • ₱<?= number_format((float)($row['scholarship_amount'] ?? 0), 2) ?></small>
            </div>
            <div>
              <span class="badge badge-status <?= $status==='approved'?'badge-approved':($status==='rejected'?'badge-rejected':'badge-pending') ?>">
                <?= strtoupper($status) ?>
              </span>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <p class="mb-1"><strong>Student:</strong> <?= htmlspecialchars($fullName) ?> (<?= htmlspecialchars($row['user_id']) ?>)</p>
              <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($row['email'] ?? '') ?></p>
              <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($row['phone'] ?? '') ?></p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><strong>Course:</strong> <?= htmlspecialchars($row['course'] ?? '') ?></p>
              <p class="mb-1"><strong>Year / Section:</strong> <?= htmlspecialchars((string)($row['year_level'] ?? '')) ?><?= !empty($row['section']) ? ' • '.htmlspecialchars($row['section']) : '' ?></p>
              <p class="mb-1"><strong>Applied:</strong> <?= date('M j, Y g:i A', strtotime($row['application_date'])) ?></p>
            </div>
          </div>
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="d-flex gap-2">
              <button class="btn btn-info btn-sm view-documents" data-application-id="<?= (int)$row['id'] ?>" data-bs-toggle="modal" data-bs-target="#viewDocumentsModal">
                <i class="fa fa-file-alt"></i> View Documents
              </button>
              <button class="btn btn-secondary btn-sm review-app" data-application-id="<?= (int)$row['id'] ?>" data-bs-toggle="modal" data-bs-target="#reviewModal">
                <i class="fa fa-clipboard-check"></i> Review
              </button>
              <a class="btn btn-secondary btn-sm" href="download_application_documents_zip.php?application_id=<?= (int)$row['id'] ?>" target="_blank">
                <i class="fa fa-download"></i> Download ZIP
              </a>
            </div>
            <div class="btn-group">
              <?php if ($status === 'pending'): ?>
                <button class="btn btn-success btn-sm do-approve" data-id="<?= (int)$row['id'] ?>"><i class="fa fa-check"></i> Approve</button>
                <button class="btn btn-danger btn-sm do-reject" data-id="<?= (int)$row['id'] ?>"><i class="fa fa-times"></i> Reject</button>
              <?php else: ?>
                <button class="btn btn-primary btn-sm do-pending" data-id="<?= (int)$row['id'] ?>"><i class="fa fa-rotate-left"></i> Set Pending</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; else: ?>
        <div class="text-center text-muted py-5"><i class="fa fa-file-alt fa-2x mb-2"></i><div>No applications found.</div></div>
      <?php endif; ?>
    </div>
    <?php if ($totalPages > 1): $qs = $_GET; ?>
    <nav class="mt-3">
      <ul class="pagination">
        <?php $qs['page'] = max(1, $page-1); ?>
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($qs)) ?>">Previous</a></li>
        <?php for ($p=1; $p <= $totalPages; $p++): $qs['page']=$p; ?>
          <li class="page-item <?= $p===$page ? 'active' : '' ?>"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($qs)) ?>"><?= $p ?></a></li>
        <?php endfor; ?>
        <?php $qs['page'] = min($totalPages, $page+1); ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?<?= htmlspecialchars(http_build_query($qs)) ?>">Next</a></li>
      </ul>
    </nav>
    <?php endif; ?>
  </div>
</div>
<!-- View Documents Modal -->
<div class="modal fade" id="viewDocumentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-file-alt me-2"></i> Application Documents</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" id="documentsModalBody">Loading...</div>
  </div></div>
</div>
<!-- Review Workspace Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-clipboard-check me-2"></i> Review Application</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" id="reviewModalBody">Loading...</div>
  </div></div>
</div>
<!-- Lightbox Modal -->
<div class="modal fade" id="docLightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen"><div class="modal-content bg-dark">
    <div class="modal-header border-0">
      <button type="button" class="btn btn-light ms-auto" data-bs-dismiss="modal">Close</button>
    </div>
    <div class="modal-body p-0 d-flex justify-content-center align-items-center" id="lightboxBody">
    </div>
  </div></div>
</div>
<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-comment-dots me-2"></i> Rejection Reason</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="rejectApplicationId" value="">
      <div class="mb-2">
        <label class="form-label">Templates</label>
        <select id="rejectTemplate" class="form-select">
          <option value="">Select a template...</option>
          <option value="Incomplete requirements">Incomplete requirements</option>
          <option value="Eligibility criteria not met">Eligibility criteria not met</option>
          <option value="Documents not clear or illegible">Documents not clear or illegible</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea id="rejectReasonText" class="form-control" rows="3" placeholder="Enter reason..." required></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-danger" id="confirmRejectBtn"><i class="fa fa-times"></i> Reject</button>
    </div>
  </div></div>
</div>
<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-comment-dots me-2"></i> Rejection Reason (Bulk)</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <div class="mb-2">
        <label class="form-label">Templates</label>
        <select id="bulkRejectTemplate" class="form-select">
          <option value="">Select a template...</option>
          <option value="Incomplete requirements">Incomplete requirements</option>
          <option value="Eligibility criteria not met">Eligibility criteria not met</option>
          <option value="Documents not clear or illegible">Documents not clear or illegible</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea id="bulkRejectReasonText" class="form-control" rows="3" placeholder="Enter reason..." required></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-danger" id="confirmBulkRejectBtn"><i class="fa fa-times"></i> Reject Selected</button>
    </div>
  </div></div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  function filterClient() {
    const q = ($('#searchInput').val() || '').toLowerCase();
    const s = ($('#statusFilter').val() || '').toLowerCase();
    const scholarshipId = $('#scholarshipFilter').val();
    const from = $('#dateFrom').val();
    const to = $('#dateTo').val();
    $('.application-item').each(function(){
      const name = $(this).data('name') || '';
      const st = $(this).data('status') || '';
      const applied = ($(this).data('applied') || '').toString();
      const cardScholarshipId = (($(this).data('scholarship-id') || '')+ '').toString();
      let show = true;
      if (q && name.indexOf(q) === -1) show = false;
      if (s && st !== s) show = false;
      if (scholarshipId && cardScholarshipId !== String(scholarshipId)) show = false;
      if (from && applied && applied < from) show = false;
      if (to && applied && applied > to) show = false;
      $(this).toggle(show);
    });
  }
  $('#searchInput,#statusFilter,#scholarshipFilter,#dateFrom,#dateTo').on('input change', filterClient);
  $('#resetFilters').on('click', function(){
    $('#searchInput').val(''); $('#statusFilter').val(''); $('#scholarshipFilter').val(''); $('#dateFrom').val(''); $('#dateTo').val('');
    window.location = 'manage_applications.php';
  });
  $('#applyFilters').on('click', function(){
    const params = new URLSearchParams(window.location.search);
    if ($('#statusFilter').val()) params.set('filter_status', $('#statusFilter').val()); else params.delete('filter_status');
    if ($('#scholarshipFilter').val()) params.set('filter_scholarship', $('#scholarshipFilter').val()); else params.delete('filter_scholarship');
    if ($('#dateFrom').val()) params.set('from', $('#dateFrom').val()); else params.delete('from');
    if ($('#dateTo').val()) params.set('to', $('#dateTo').val()); else params.delete('to');
    window.location = 'manage_applications.php?' + params.toString();
  });
  $(document).on('click', '.view-documents', function(){
    const id = $(this).data('application-id');
    $('#documentsModalBody').html('Loading...');
    $.get('get_application_documents.php', { application_id: id })
     .done(html => $('#documentsModalBody').html(html))
     .fail(() => $('#documentsModalBody').html('<div class="alert alert-danger">Error loading documents.</div>'));
  });
  // Handle document delete inside modal
  $(document).on('click', '.delete-document', function(){
    if (!confirm('Delete this document?')) return;
    const docId = $(this).data('document-id');
    const appId = $('.view-documents[data-bs-target="#viewDocumentsModal"]').data('application-id') || $('#rejectApplicationId').val();
    $.post('delete_application_document.php', { document_id: docId, csrf_token: $('#csrfToken').val() })
      .done(resp => {
        if (resp && resp.status === 'success') {
          $('#documentsModalBody').html('Loading...');
          $.get('get_application_documents.php', { application_id: appId })
            .done(html => $('#documentsModalBody').html(html))
            .fail(() => $('#documentsModalBody').html('<div class="alert alert-danger">Error loading documents.</div>'));
        } else {
          alert((resp && resp.message) || 'Delete failed.');
        }
      })
      .fail(() => alert('Delete request failed.'));
  });
  function postAction(action, id, extraData) {
    const btn = $('[data-id="'+id+'"].do-'+action.replace('_','-') );
    const original = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
    const payload = Object.assign({ action, application_id: id, csrf_token: $('#csrfToken').val() }, extraData || {});
    $.post('manage_applications.php', payload)
      .done(resp => { if (resp && resp.status === 'success') location.reload(); else alert((resp && resp.message) || 'Action failed.'); })
      .fail(() => alert('Request failed.'))
      .always(() => btn.prop('disabled', false).html(original));
  }
  $(document).on('click', '.do-approve', function(){ postAction('approve', $(this).data('id')); });
  $(document).on('click', '.do-pending', function(){ postAction('set_pending', $(this).data('id')); });
  // Review workspace loader
  $(document).on('click', '.review-app', function(){
    const id = $(this).data('application-id');
    $('#reviewModalBody').html('Loading...');
    $.get('get_application_review.php', { application_id: id })
      .done(html => $('#reviewModalBody').html(html))
      .fail(() => $('#reviewModalBody').html('<div class="alert alert-danger">Error loading review workspace.</div>'));
  });
  // Lightbox for docs
  $(document).on('click', '.doc-thumb', function(){
    const src = $(this).data('view-src');
    const el = `<img src="${src}" style="max-width:100%; max-height:100%;" />`;
    $('#lightboxBody').html(el);
    new bootstrap.Modal(document.getElementById('docLightboxModal')).show();
  });
  $(document).on('click', '.open-lightbox', function(){
    const src = $(this).data('view-src');
    const type = $(this).data('type') || 'pdf';
    const el = type === 'pdf' ? `<embed src="${src}" type="application/pdf" width="100%" height="100%" />` : `<img src="${src}" style="max-width:100%; max-height:100%;" />`;
    $('#lightboxBody').html(el);
    new bootstrap.Modal(document.getElementById('docLightboxModal')).show();
  });
  // Reject with reason + templates
  let rejectModal;
  $(document).on('click', '.do-reject', function(){
    $('#rejectApplicationId').val($(this).data('id'));
    $('#rejectReasonText').val('');
    $('#rejectTemplate').val('');
    rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
    rejectModal.show();
  });
  $('#rejectTemplate').on('change', function(){ const t = $(this).val(); if (t) $('#rejectReasonText').val(t); });
  $('#confirmRejectBtn').on('click', function(){
    const id = $('#rejectApplicationId').val();
    const reason = ($('#rejectReasonText').val() || '').trim();
    if (!reason) { alert('Please provide a rejection reason.'); return; }
    postAction('reject', id, { rejection_reason: reason });
    if (rejectModal) rejectModal.hide();
  });
  // Bulk selection
  $('#selectAll').on('change', function(){ $('.select-app').prop('checked', this.checked); });
  function getSelectedIds(){ return $('.select-app:checked').map(function(){ return $(this).val(); }).get(); }
  function postBulk(action, extra) {
    const ids = getSelectedIds();
    if (!ids.length) { alert('No applications selected.'); return; }
    const payload = Object.assign({ bulk_action: action, ids: ids, csrf_token: $('#csrfToken').val() }, extra || {});
    $.post('manage_applications.php', payload)
      .done(resp => { if (resp && resp.status === 'success') location.reload(); else alert((resp && resp.message) || 'Bulk action failed.'); })
      .fail(() => alert('Request failed.'));
  }
  $('#bulkApprove').on('click', function(){ postBulk('approve'); });
  $('#bulkPending').on('click', function(){ postBulk('pending'); });
  let bulkRejectModal;
  $('#bulkReject').on('click', function(){
    if (!getSelectedIds().length) { alert('No applications selected.'); return; }
    $('#bulkRejectReasonText').val('');
    $('#bulkRejectTemplate').val('');
    bulkRejectModal = new bootstrap.Modal(document.getElementById('bulkRejectModal'));
    bulkRejectModal.show();
  });
  $('#bulkRejectTemplate').on('change', function(){ const t = $(this).val(); if (t) $('#bulkRejectReasonText').val(t); });
  $('#confirmBulkRejectBtn').on('click', function(){
    const reason = ($('#bulkRejectReasonText').val() || '').trim();
    if (!reason) { alert('Please provide a rejection reason.'); return; }
    postBulk('reject', { rejection_reason: reason });
    if (bulkRejectModal) bulkRejectModal.hide();
  });
  filterClient();
})();
</script>
</body>
</html>
