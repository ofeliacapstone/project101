<?php
session_start();
require_once 'config.php';
include 'admin_dormitory_header.php';

// Security: Check user session and role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Dormitory Admin') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

// CSRF token setup
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Allowed filters and validation
$allowedStatuses = ['All', 'Pending', 'Verified', 'Rejected'];
$status = isset($_GET['status']) && in_array($_GET['status'], $allowedStatuses) ? $_GET['status'] : 'All';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 25, 50]) ? (int)$_GET['per_page'] : 10;
$offset = ($page - 1) * $perPage;
$sort = isset($_GET['sort']) && in_array($_GET['sort'], ['submitted_at', 'amount']) ? $_GET['sort'] : 'submitted_at';
$order = isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC']) ? $_GET['order'] : 'DESC';

// Date filtering
$fromDate = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : '';
$toDate = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';

// Build WHERE clause
$where = [];
$params = [];
$types = '';

if ($status !== 'All') {
    $where[] = 'p.status = ?';
    $params[] = $status;
    $types .= 's';
}
if ($search !== '') {
    $where[] = "(p.student_id LIKE CONCAT('%', ?, '%') OR u.first_name LIKE CONCAT('%', ?, '%') OR u.last_name LIKE CONCAT('%', ?, '%'))";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= 'sss';
}
if ($fromDate) {
    $where[] = "DATE(p.submitted_at) >= ?";
    $params[] = $fromDate;
    $types .= 's';
}
if ($toDate) {
    $where[] = "DATE(p.submitted_at) <= ?";
    $params[] = $toDate;
    $types .= 's';
}

$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT p.id, p.student_id, p.room_id, p.amount, p.receipt_path, p.status, p.submitted_at, p.remarks,
            TRIM(CONCAT(u.first_name,' ',COALESCE(NULLIF(u.middle_name,''),''),' ',u.last_name)) AS full_name,
            r.name AS room_name
        FROM payments p
        JOIN users u ON p.student_id = u.user_id
        LEFT JOIN rooms r ON p.room_id = r.id
        $whereSql
        ORDER BY $sort $order
        LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

try {
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }

    // Get total records for pagination
    $countSql = "SELECT COUNT(*) as total FROM payments p JOIN users u ON p.student_id = u.user_id $whereSql";
    $countParams = $params;
    $countTypes = $types;
    if (strlen($countTypes) >= 2 && substr($countTypes, -2) === 'ii') {
        $countTypes = substr($countTypes, 0, -2);
        $countParams = array_slice($countParams, 0, -2);
    }
    $countStmt = $conn->prepare($countSql);
    if ($countTypes !== '') {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Build counters for tabs (respecting current search and date filters but ignoring selected status)
    $baseWhere = [];
    $baseParams = [];
    $baseTypes = '';
    if ($search !== '') {
        $baseWhere[] = "(p.student_id LIKE CONCAT('%', ?, '%') OR u.first_name LIKE CONCAT('%', ?, '%') OR u.last_name LIKE CONCAT('%', ?, '%'))";
        $baseParams[] = $search;
        $baseParams[] = $search;
        $baseParams[] = $search;
        $baseTypes .= 'sss';
    }
    if ($fromDate) {
        $baseWhere[] = "DATE(p.submitted_at) >= ?";
        $baseParams[] = $fromDate;
        $baseTypes .= 's';
    }
    if ($toDate) {
        $baseWhere[] = "DATE(p.submitted_at) <= ?";
        $baseParams[] = $toDate;
        $baseTypes .= 's';
    }
    $baseWhereSql = count($baseWhere) ? ('WHERE ' . implode(' AND ', $baseWhere)) : '';
    $counts = ['All' => 0, 'Pending' => 0, 'Verified' => 0, 'Rejected' => 0];
    // Count All
    $allCountSql = "SELECT COUNT(*) AS c FROM payments p JOIN users u ON p.student_id = u.user_id $baseWhereSql";
    $allStmt = $conn->prepare($allCountSql);
    if ($baseTypes !== '') { $allStmt->bind_param($baseTypes, ...$baseParams); }
    $allStmt->execute();
    $counts['All'] = (int)($allStmt->get_result()->fetch_assoc()['c'] ?? 0);
    // Count by status
    foreach (['Pending','Verified','Rejected'] as $st) {
        $wSql = $baseWhereSql . (strlen($baseWhereSql) ? ' AND ' : ' WHERE ') . 'p.status = ?';
        $cSql = "SELECT COUNT(*) AS c FROM payments p JOIN users u ON p.student_id = u.user_id $wSql";
        $stmtC = $conn->prepare($cSql);
        if ($baseTypes !== '') {
            $bindTypes = $baseTypes . 's';
            $bindParams = array_merge($baseParams, [$st]);
            $stmtC->bind_param($bindTypes, ...$bindParams);
        } else {
            $stmtC->bind_param('s', $st);
        }
        $stmtC->execute();
        $counts[$st] = (int)($stmtC->get_result()->fetch_assoc()['c'] ?? 0);
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred while fetching data. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory Payments | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --neust-blue:#004AAD;
            --neust-gold:#FFD700;
            --neust-bg:#f5f7fb;
            --card-radius:14px;
            --shadow:0 10px 24px rgba(0,0,0,.06);
        }
        body{font-family:'Inter',system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--neust-bg);}    
        .page-title{font-weight:800;letter-spacing:.2px;color:#0d1b2a}
        .neust-card{background:#fff;border:none;border-radius:var(--card-radius);box-shadow:var(--shadow)}
        .tabs-nav{gap:8px}
        .tabs-nav .nav-link{border:none;border-radius:999px;padding:.5rem .9rem;color:#0d1b2a;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,.04)}
        .tabs-nav .nav-link.active{background:var(--neust-blue);color:#fff}
        .counter-badge{background:var(--neust-gold);color:#0b2e59;border-radius:999px;padding:.15rem .5rem;font-weight:700;margin-left:.5rem}
        .toolbar .form-control,.toolbar .form-select{border-radius:999px}
        .btn-export{border-radius:999px;border:1px solid var(--neust-blue);color:var(--neust-blue);background:#fff}
        .btn-export:hover{background:var(--neust-blue);color:#fff}
        .split{display:grid;grid-template-columns:1fr 1.1fr;gap:16px}
        @media (max-width: 992px){ .split{ grid-template-columns:1fr; } }
        .list-pane{max-height:70vh;overflow:auto}
        .payment-item{border:1px solid #e9eef5;border-radius:12px;padding:12px 14px;margin-bottom:10px;cursor:pointer;background:#fff;transition:box-shadow .15s ease, transform .12s ease,border-color .15s ease}
        .payment-item:hover{box-shadow:0 8px 22px rgba(0,0,0,.06);border-color:var(--neust-gold)}
        .payment-item.active{outline:2px solid var(--neust-blue);box-shadow:0 10px 26px rgba(0,0,0,.08)}
        .student-name{font-weight:700;color:#0d1b2a}
        .student-id{color:#6c757d;font-size:.85rem}
        .room-chip{background:#e9f0ff;color:#0b3e91;border-radius:999px;padding:.2rem .55rem;font-weight:600}
        .amount{font-weight:800;color:#0b3e91}
        .date-sub{color:#6b7280;font-size:.9rem}
        .status-badge{border-radius:999px;padding:.2rem .6rem;font-weight:700}
        .status-Pending{background:#fff3cd;color:#846205}
        .status-Verified{background:#d1e7dd;color:#0f5132}
        .status-Rejected{background:#f8d7da;color:#842029}
        .details .card-header{background:linear-gradient(135deg,var(--neust-blue) 0%, #0b5ed7 100%);color:#fff;border-radius:12px 12px 0 0}
        .receipt-thumb{border:2px solid var(--neust-blue);border-radius:10px;overflow:hidden;display:inline-block}
        .receipt-thumb img{max-width:100%;height:auto;display:block}
        .btn-approve{background:var(--neust-blue);color:#fff;border:none}
        .btn-approve:hover{opacity:.92}
        .btn-reject{border:2px solid var(--neust-gold);color:var(--neust-blue);background:#fff}
        .btn-reject:hover{background:var(--neust-gold);color:#0b2e59}
        .form-control:focus,.btn:focus{box-shadow:0 0 0 .15rem rgba(0,74,173,.18)}
        .number{text-align:right}
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="page-title mb-0"><i class="bi bi-cash-coin text-primary"></i> Dormitory Payments</h2>
        <div class="toolbar d-flex align-items-center gap-2 flex-wrap">
            <form class="d-flex align-items-center gap-2" method="get" id="filterForm">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <input type="hidden" name="per_page" value="<?= (int)$perPage ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search student or ID">
                </div>
                <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($fromDate) ?>" title="From">
                <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($toDate) ?>" title="To">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i></button>
            </form>
            <button type="button" id="exportCsv" class="btn btn-export"><i class="bi bi-download"></i> Export</button>
        </div>
    </div>

    <ul class="nav tabs-nav mb-3">
        <?php
            $qsBase = http_build_query([
                'q' => $search,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'per_page' => $perPage,
                'sort' => $sort,
                'order' => $order,
            ]);
            foreach ($allowedStatuses as $s):
                $active = ($status === $s) ? 'active' : '';
                $href = '?status=' . urlencode($s) . '&' . $qsBase;
                $c = (int)($counts[$s] ?? 0);
        ?>
        <li class="nav-item"><a class="nav-link <?= $active ?>" href="<?= $href ?>"><?= htmlspecialchars($s) ?><span class="counter-badge"><?= $c ?></span></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="split">
        <div>
            <div class="list-pane">
                <?php if (empty($rows)): ?>
                    <div class="neust-card p-5 text-center text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found.</div>
                <?php else: ?>
                    <?php foreach ($rows as $p): ?>
                        <div class="payment-item" tabindex="0"
                             data-id="<?= (int)$p['id'] ?>"
                             data-student="<?= htmlspecialchars($p['full_name']) ?>"
                             data-studentid="<?= htmlspecialchars($p['student_id']) ?>"
                             data-room="<?= htmlspecialchars($p['room_name'] ?? ('Room #' . (int)$p['room_id'])) ?>"
                             data-amount="<?= htmlspecialchars(number_format((float)$p['amount'],2,'.','')) ?>"
                             data-date="<?= htmlspecialchars($p['submitted_at']) ?>"
                             data-status="<?= htmlspecialchars($p['status']) ?>"
                             data-remarks="<?= htmlspecialchars($p['remarks'] ?? '') ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="student-name"><?= htmlspecialchars($p['full_name']) ?> <span class="student-id">• <?= htmlspecialchars($p['student_id']) ?></span></div>
                                    <div class="mt-1"><span class="room-chip"><i class="bi bi-door-open"></i> <?= htmlspecialchars($p['room_name'] ?? ('Room #' . (int)$p['room_id'])) ?></span></div>
                                </div>
                                <div class="text-end">
                                    <div class="amount">₱<?= htmlspecialchars(number_format((float)$p['amount'],2)) ?></div>
                                    <div class="date-sub"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($p['submitted_at']))) ?></div>
                                    <span class="status-badge status-<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Pagination" class="mt-3">
                <ul class="pagination pagination-sm">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=1">First</a></li>
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page - 1 ?>">Prev</a></li>
                    <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page + 1 ?>">Next</a></li>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $totalPages ?>">Last</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <div>
            <div class="details">
                <div class="neust-card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div><i class="bi bi-person-badge"></i> Student Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted">Name</div>
                                <div id="dName" class="fw-bold">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">ID</div>
                                <div id="dStudentId" class="fw-semibold">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">Room</div>
                                <div id="dRoom" class="fw-semibold">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="neust-card mb-3">
                    <div class="card-header"><i class="bi bi-receipt"></i> Payment Info</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted">Amount</div>
                                <div id="dAmount" class="fw-bold number">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted">Date Submitted</div>
                                <div id="dDate" class="fw-semibold">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted">Purpose</div>
                                <div class="fw-semibold">Dormitory Payment</div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-muted">Status</div>
                                <div id="dStatusWrap"><span id="dStatus" class="status-badge">—</span></div>
                                <div id="dRemarks" class="mt-2 text-muted small"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="neust-card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between"><span><i class="bi bi-image"></i> Receipt</span>
                        <div>
                            <button type="button" id="btnViewReceipt" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrows-fullscreen"></i> Preview</button>
                            <a id="btnOpenReceipt" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="receipt-thumb" id="receiptThumb" style="display:none">
                            <img id="receiptImg" alt="Receipt">
                        </div>
                        <div id="noReceipt" class="text-muted">No receipt uploaded.</div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-approve" id="btnApprove"><i class="bi bi-check-circle"></i> Approve</button>
                    <button class="btn btn-reject" id="btnReject"><i class="bi bi-x-circle"></i> Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> Receipt Preview</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="receiptModalBody">
                <div class="spinner-border text-primary my-4" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
            <div class="modal-footer">
                <a id="downloadReceipt" class="btn btn-outline-primary" download><i class="bi bi-download"></i> Download</a>
            </div>
        </div>
    </div>
    </div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle"></i> Reject Payment</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Remarks</label>
                <textarea id="rejectRemarks" class="form-control" rows="3" placeholder="Provide reason for rejection" required></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirmReject">Reject</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header"><strong class="me-auto">Notification</strong><button class="btn-close" data-bs-dismiss="toast"></button></div>
        <div class="toast-body"></div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
(function(){
    const csrf = '<?= htmlspecialchars($_SESSION['csrf_token']) ?>';
    const items = Array.from(document.querySelectorAll('.payment-item'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    const showToast = (msg)=>{ document.querySelector('#actionToast .toast-body').textContent = msg; toast.show(); };

    const state = { id:null, receiptUrl:null, status:null };
    const dName = document.getElementById('dName');
    const dStudentId = document.getElementById('dStudentId');
    const dRoom = document.getElementById('dRoom');
    const dAmount = document.getElementById('dAmount');
    const dDate = document.getElementById('dDate');
    const dStatus = document.getElementById('dStatus');
    const dRemarks = document.getElementById('dRemarks');
    const btnApprove = document.getElementById('btnApprove');
    const btnReject = document.getElementById('btnReject');
    const btnViewReceipt = document.getElementById('btnViewReceipt');
    const btnOpenReceipt = document.getElementById('btnOpenReceipt');
    const receiptThumb = document.getElementById('receiptThumb');
    const receiptImg = document.getElementById('receiptImg');
    const noReceipt = document.getElementById('noReceipt');
    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

    function setActive(el){ items.forEach(i=>i.classList.remove('active')); if (el){ el.classList.add('active'); el.scrollIntoView({block:'nearest'}); } }
    function setActionsEnabled(){
        const isPending = state.status === 'Pending';
        btnApprove.disabled = !isPending; btnReject.disabled = !isPending;
    }
    function renderDetails(el){
        const amount = parseFloat(el.dataset.amount || '0');
        state.id = el.dataset.id; state.status = el.dataset.status;
        dName.textContent = el.dataset.student; dStudentId.textContent = el.dataset.studentid; dRoom.textContent = el.dataset.room;
        dAmount.textContent = '₱' + amount.toFixed(2); dDate.textContent = new Date(el.dataset.date).toLocaleString();
        dStatus.textContent = el.dataset.status; dStatus.className = 'status-badge status-' + el.dataset.status;
        dRemarks.textContent = el.dataset.remarks ? ('Remarks: ' + el.dataset.remarks) : '';
        state.receiptUrl = `view_payment_file.php?id=${encodeURIComponent(state.id)}&csrf_token=${encodeURIComponent(csrf)}`;
        btnOpenReceipt.href = state.receiptUrl;
        // Try to show thumb (image only)
        fetch(state.receiptUrl, { method:'HEAD' }).then(r=>{
            const mt = r.headers.get('Content-Type')||'';
            if (mt.startsWith('image/')){ receiptThumb.style.display='inline-block'; receiptImg.src = state.receiptUrl; noReceipt.style.display='none'; }
            else if (mt === 'application/pdf'){ receiptThumb.style.display='none'; noReceipt.textContent='PDF receipt available.'; noReceipt.style.display='block'; }
            else { receiptThumb.style.display='none'; noReceipt.textContent='No receipt uploaded.'; noReceipt.style.display='block'; }
        }).catch(()=>{ receiptThumb.style.display='none'; noReceipt.textContent='No receipt uploaded.'; noReceipt.style.display='block'; });
        setActionsEnabled();
    }
    items.forEach(el=>{ el.addEventListener('click', ()=>{ setActive(el); renderDetails(el); }); el.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); setActive(el); renderDetails(el); } }); });
    if (items.length){ setActive(items[0]); renderDetails(items[0]); }

    btnViewReceipt.addEventListener('click', function(){
        const body = document.getElementById('receiptModalBody');
        body.innerHTML = '<div class="spinner-border text-primary my-4" role="status"><span class="visually-hidden">Loading...</span></div>';
        fetch(state.receiptUrl).then(r=>r.blob()).then(blob=>{
            const url = URL.createObjectURL(blob); let content='';
            if (blob.type.startsWith('image/')) content = `<img src="${url}" class="img-fluid rounded shadow-sm" alt="Receipt">`;
            else if (blob.type==='application/pdf') content = `<embed src="${url}" type="application/pdf" width="100%" height="520px">`;
            else content = '<div class="text-danger">Unable to preview this file type.</div>';
            body.innerHTML = content; const dl = document.getElementById('downloadReceipt'); dl.href = url; dl.download = `receipt_${state.id}`;
        }).catch(()=>{ body.innerHTML = '<div class="text-danger">Failed to load receipt preview.</div>'; });
        receiptModal.show();
    });

    btnApprove.addEventListener('click', function(){
        if (!state.id) return; this.disabled=true; this.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span> Approving...';
        const body = new URLSearchParams({ id: state.id, action:'verify', csrf_token: csrf });
        fetch('process_student_payment.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body })
          .then(r=>r.json()).then(j=>{ showToast(j.message || (j.success?'Payment approved':'Action failed')); if(j.success) setTimeout(()=>location.reload(), 1200); })
          .catch(()=> showToast('Network error occurred. Please try again.'))
          .finally(()=>{ this.disabled=false; this.innerHTML='<i class="bi bi-check-circle"></i> Approve'; });
    });
    btnReject.addEventListener('click', ()=>{ document.getElementById('rejectRemarks').value=''; rejectModal.show(); });
    document.getElementById('confirmReject').addEventListener('click', function(){
        const remarks = document.getElementById('rejectRemarks').value.trim(); if (!remarks) { document.getElementById('rejectRemarks').focus(); return; }
        this.disabled=true; this.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span> Rejecting...';
        const body = new URLSearchParams({ id: state.id, action:'reject', remarks, csrf_token: csrf });
        fetch('process_student_payment.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body })
          .then(r=>r.json()).then(j=>{ showToast(j.message || (j.success?'Payment rejected':'Action failed')); if(j.success) setTimeout(()=>location.reload(), 1200); })
          .catch(()=> showToast('Network error occurred. Please try again.'))
          .finally(()=>{ this.disabled=false; this.innerHTML='Reject'; rejectModal.hide(); });
    });

    // CSV export for current list (client-side)
    document.getElementById('exportCsv').addEventListener('click', ()=>{
        if (!items.length) { showToast('No data to export'); return; }
        const rows = [['Student Name','Student ID','Room','Amount','Date Submitted','Status']];
        items.forEach(el=>{ rows.push([el.dataset.student, el.dataset.studentid, el.dataset.room, el.dataset.amount, el.dataset.date, el.dataset.status]); });
        const csv = rows.map(r=> r.map(v=> '"' + String(v).replaceAll('"','""') + '"').join(',')).join('\n');
        const blob = new Blob([csv], {type:'text/csv'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href=url; a.download='payments_export.csv'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    });
})();
</script>
</body>
</html>