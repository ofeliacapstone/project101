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
    <title>Student Payments | Dormitory Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .loading-row {
            opacity: 0.5;
            pointer-events: none;
        }
        .toast-container {
            z-index: 1055;
        }
        .sort-icon::after {
            content: '\f0dc';
            font-family: 'Bootstrap Icons';
            margin-left: 5px;
            vertical-align: middle;
        }
        .sort-asc::after { content: '\f0dd'; }
        .sort-desc::after { content: '\f0de'; }
        @media (max-width: 576px) {
            .table th, .table td { font-size: 0.85rem; }
            .btn-sm { font-size: 0.75rem; }
            .form-control, .form-select { font-size: 0.9rem; }
        }
        .form-control:focus, .btn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="card p-4">
        <h2 class="mb-4">Student Payments</h2>
        <form class="row g-3 mb-4" method="get" id="filter-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="col-md-3 col-sm-12">
                <label for="status-select" class="visually-hidden">Status</label>
                <select name="status" id="status-select" class="form-select">
                    <?php foreach ($allowedStatuses as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5 col-sm-12">
                <label for="search-input" class="visually-hidden">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" id="search-input" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="e.g., John Doe or ID123">
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <label for="per-page" class="visually-hidden">Records per page</label>
                <select name="per_page" id="per-page" class="form-select">
                    <?php foreach ([10, 25, 50] as $pp): ?>
                    <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
            <div class="col-12 mt-2">
                <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#advanced-filters">Advanced Filters</button>
                <div class="collapse" id="advanced-filters">
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="from-date" class="form-label">From Date</label>
                            <input type="date" name="from_date" id="from-date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="to-date" class="form-label">To Date</label>
                            <input type="date" name="to_date" id="to-date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" role="grid" aria-describedby="payments-table-info">
                <thead>
                    <tr>
                        <th scope="col">Student</th>
                        <th scope="col">Room</th>
                        <th scope="col"><a href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=amount&order=<?= $sort === 'amount' && $order === 'ASC' ? 'DESC' : 'ASC' ?>" class="text-decoration-none <?= $sort === 'amount' ? ($order === 'ASC' ? 'sort-asc' : 'sort-desc') : 'sort-icon' ?>">Amount</a></th>
                        <th scope="col"><a href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=submitted_at&order=<?= $sort === 'submitted_at' && $order === 'ASC' ? 'DESC' : 'ASC' ?>" class="text-decoration-none <?= $sort === 'submitted_at' ? ($order === 'ASC' ? 'sort-asc' : 'sort-desc') : 'sort-icon' ?>">Submitted</a></th>
                        <th scope="col">Receipt</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 fade-in">
                            <i class="bi bi-inbox fs-3 text-muted"></i>
                            <p class="mb-0">No records found. Try broadening your search.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($rows as $p): ?>
                    <tr class="fade-in" data-payment-id="<?= (int)$p['id'] ?>">
                        <td data-bs-toggle="tooltip" title="<?= htmlspecialchars($p['full_name']) ?>">
                            <?= strlen($p['full_name']) > 20 ? htmlspecialchars(substr($p['full_name'], 0, 20)) . '...' : htmlspecialchars($p['full_name']) ?>
                            <br><small><?= htmlspecialchars($p['student_id']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($p['room_name'] ?? ('Room #' . (int)$p['room_id'])) ?></td>
                        <td>₱<?= htmlspecialchars(number_format((float)$p['amount'], 2)) ?></td>
                        <td><?= htmlspecialchars($p['submitted_at']) ?></td>
                        <td><a class="btn btn-sm btn-outline-secondary" href="view_payment_file.php?id=<?= (int)$p['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" target="_blank" aria-label="View receipt for <?= htmlspecialchars($p['student_id']) ?>"><i class="bi bi-file-earmark-text"></i></a></td>
                        <td><span class="badge bg-<?= $p['status'] === 'Verified' ? 'success' : ($p['status'] === 'Rejected' ? 'danger' : 'warning') ?>" aria-label="Status: <?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                        <td>
                            <?php if ($p['status'] === 'Pending'): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item action-btn" data-action="verify" data-id="<?= (int)$p['id'] ?>">Verify</button></li>
                                    <li><button class="dropdown-item action-btn" data-action="reject" data-id="<?= (int)$p['id'] ?>">Reject</button></li>
                                </ul>
                            </div>
                            <?php elseif ($p['status'] === 'Rejected' && !empty($p['remarks'])): ?>
                            <button class="btn btn-link btn-sm text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#remarks-<?= (int)$p['id'] ?>">View Remarks</button>
                            <div class="collapse" id="remarks-<?= (int)$p['id'] ?>">
                                <small class="text-muted"><?= htmlspecialchars($p['remarks']) ?></small>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=1" aria-label="First page">First</a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page - 1 ?>" aria-label="Previous page">Previous</a>
                </li>
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                if ($startPage > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($endPage < $totalPages) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $page + 1 ?>" aria-label="Next page">Next</a>
                </li>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?status=<?= $status ?>&q=<?= urlencode($search) ?>&per_page=<?= $perPage ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $totalPages ?>" aria-label="Last page">Last</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modal-message"></p>
                <div id="reject-reason" class="d-none">
                    <label for="reject-reason-input" class="form-label">Reason for Rejection</label>
                    <textarea id="reject-reason-input" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-action">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize modal and toast
    const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
    const actionToast = new bootstrap.Toast(document.getElementById('actionToast'));

    // Handle action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            const row = document.querySelector(`tr[data-payment-id="${id}"]`);
            const modalMessage = document.getElementById('modal-message');
            const rejectReasonDiv = document.getElementById('reject-reason');
            const rejectReasonInput = document.getElementById('reject-reason-input');

            modalMessage.textContent = action === 'verify' ? 'Are you sure you want to verify this payment?' : 'Are you sure you want to reject this payment?';
            rejectReasonDiv.classList.toggle('d-none', action !== 'reject');
            if (action === 'reject') rejectReasonInput.value = '';

            document.getElementById('confirm-action').onclick = function() {
                let body = new URLSearchParams({
                    id: id,
                    action: action,
                    csrf_token: '<?= htmlspecialchars($_SESSION['csrf_token']) ?>'
                });
                if (action === 'reject') {
                    const reason = rejectReasonInput.value.trim();
                    if (!reason) {
                        rejectReasonInput.classList.add('is-invalid');
                        return;
                    }
                    body.append('remarks', reason);
                }

                row.classList.add('loading-row');
                btn.disabled = true;
                const spinner = document.createElement('span');
                spinner.className = 'spinner-border spinner-border-sm me-2';
                btn.prepend(spinner);

                fetch('process_student_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body
                })
                .then(r => r.json())
                .then(data => {
                    actionToast._element.querySelector('.toast-body').textContent = data.message || (data.success ? `Payment ${action}ed successfully` : 'Action failed');
                    actionToast.show();
                    if (data.success) setTimeout(() => location.reload(), 1500);
                })
                .catch(() => {
                    actionToast._element.querySelector('.toast-body').textContent = 'Network error occurred. Please try again.';
                    actionToast.show();
                })
                .finally(() => {
                    row.classList.remove('loading-row');
                    btn.disabled = false;
                    spinner.remove();
                    actionModal.hide();
                });
            };

            actionModal.show();
        });
    });

    // Form validation
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        const searchInput = document.getElementById('search-input');
        if (searchInput.value.length > 100) {
            e.preventDefault();
            searchInput.classList.add('is-invalid');
            searchInput.nextElementSibling.textContent = 'Search term is too long.';
        }
    });
});
</script>
</body>
</html>