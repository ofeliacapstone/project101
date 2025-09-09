<?php
session_start();
require_once 'config.php';
include 'admin_dormitory_header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Dormitory Admin') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

$status = isset($_GET['status']) ? $_GET['status'] : 'All';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = [];
$params = [];
$types = '';

if ($status !== 'All') { $where[] = 'p.status = ?'; $params[] = $status; $types .= 's'; }
if ($search !== '') {
    $where[] = "(p.user_id LIKE CONCAT('%', ?, '%') OR u.first_name LIKE CONCAT('%', ?, '%') OR u.last_name LIKE CONCAT('%', ?, '%') OR p.receipt_number LIKE CONCAT('%', ?, '%'))";
    $params[] = $search; $params[] = $search; $params[] = $search; $params[] = $search; $types .= 'ssss';
}

$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT p.id, p.user_id, p.period, p.amount, p.receipt_number, p.date_paid, p.receipt_file, p.status, p.verified_by, p.verified_at, p.remarks, p.created_at,
            TRIM(CONCAT(u.first_name,' ',COALESCE(NULLIF(u.middle_name,''),''),' ',u.last_name)) AS full_name
        FROM dormitory_payments p JOIN users u ON p.user_id = u.user_id $whereSql ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== '') { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($r = $result->fetch_assoc()) { $rows[] = $r; }

// Load student-side payments table as well
$where2 = [];
$params2 = [];
$types2 = '';
if ($status !== 'All') { $where2[] = 'p.status = ?'; $params2[] = $status; $types2 .= 's'; }
if ($search !== '') {
    $where2[] = "(p.student_id LIKE CONCAT('%', ?, '%') OR u.first_name LIKE CONCAT('%', ?, '%') OR u.last_name LIKE CONCAT('%', ?, '%'))";
    $params2[] = $search; $params2[] = $search; $params2[] = $search; $types2 .= 'sss';
}
$whereSql2 = count($where2) ? ('WHERE ' . implode(' AND ', $where2)) : '';
$sql2 = "SELECT p.id, p.student_id, p.room_id, p.amount, p.receipt_path, p.status, p.submitted_at,
            TRIM(CONCAT(u.first_name,' ',COALESCE(NULLIF(u.middle_name,''),''),' ',u.last_name)) AS full_name,
            r.name AS room_name
        FROM payments p
        JOIN users u ON p.student_id = u.user_id
        LEFT JOIN rooms r ON p.room_id = r.id
        $whereSql2
        ORDER BY p.submitted_at DESC";
$stmt2 = $conn->prepare($sql2);
if ($types2 !== '') { $stmt2->bind_param($types2, ...$params2); }
$stmt2->execute();
$res2 = $stmt2->get_result();
$rows2 = [];
while ($rr = $res2->fetch_assoc()) { $rows2[] = $rr; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-3">
    <h2>Dormitory Payments</h2>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <?php $statuses = ['All','Pending','Verified','Rejected']; foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by student, user id, or receipt #">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
        </div>
    </form>

    <h5>Accounting-Backed Payments (dormitory_payments)</h5>
    <div class="table-responsive mb-4">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Period</th>
                    <th>Amount</th>
                    <th>Receipt #</th>
                    <th>Date Paid</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['full_name']) ?><br><small><?= htmlspecialchars($p['user_id']) ?></small></td>
                    <td><?= htmlspecialchars($p['period']) ?></td>
                    <td>₱<?= htmlspecialchars(number_format((float)$p['amount'],2)) ?></td>
                    <td><?= htmlspecialchars($p['receipt_number']) ?></td>
                    <td><?= htmlspecialchars($p['date_paid']) ?></td>
                    <td><a class="btn btn-sm btn-outline-secondary" href="view_payment_receipt.php?id=<?= (int)$p['id'] ?>" target="_blank">View</a></td>
                    <td><span class="badge bg-<?= $p['status']==='Verified'?'success':($p['status']==='Rejected'?'danger':'warning') ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                    <td>
                        <?php if ($p['status'] === 'Pending'): ?>
                        <button class="btn btn-sm btn-success me-1" data-action="verify" data-endpoint="process_dorm_payment.php" data-id="<?= (int)$p['id'] ?>">Verify</button>
                        <button class="btn btn-sm btn-danger" data-action="reject" data-endpoint="process_dorm_payment.php" data-id="<?= (int)$p['id'] ?>">Reject</button>
                        <?php else: ?>
                        <small>Updated by: <?= htmlspecialchars($p['verified_by'] ?? '-') ?> on <?= htmlspecialchars($p['verified_at'] ?? '-') ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h5>Student Uploaded Payments (payments)</h5>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Room</th>
                    <th>Amount</th>
                    <th>Submitted</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows2 as $p2): ?>
                <tr>
                    <td><?= htmlspecialchars($p2['full_name']) ?><br><small><?= htmlspecialchars($p2['student_id']) ?></small></td>
                    <td><?= htmlspecialchars($p2['room_name'] ?? ('Room #' . (int)$p2['room_id'])) ?></td>
                    <td>₱<?= htmlspecialchars(number_format((float)$p2['amount'],2)) ?></td>
                    <td><?= htmlspecialchars($p2['submitted_at']) ?></td>
                    <td><a class="btn btn-sm btn-outline-secondary" href="view_payment_file.php?id=<?= (int)$p2['id'] ?>" target="_blank">View</a></td>
                    <td><span class="badge bg-<?= $p2['status']==='Verified'?'success':($p2['status']==='Rejected'?'danger':'warning') ?>"><?= htmlspecialchars($p2['status']) ?></span></td>
                    <td>
                        <?php if ($p2['status'] === 'Pending'): ?>
                        <button class="btn btn-sm btn-success me-1" data-action="verify" data-endpoint="process_student_payment.php" data-id="<?= (int)$p2['id'] ?>">Verify</button>
                        <button class="btn btn-sm btn-danger" data-action="reject" data-endpoint="process_student_payment.php" data-id="<?= (int)$p2['id'] ?>">Reject</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('button[data-action]').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            const endpoint = this.getAttribute('data-endpoint') || 'process_dorm_payment.php';
            let body = new URLSearchParams({id, action});
            if (action === 'reject' && endpoint === 'process_dorm_payment.php') {
                const reason = prompt('Reason for rejection:');
                if (reason === null) return;
                body.append('remarks', reason);
            }
            fetch(endpoint, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body })
            .then(r => r.json())
            .then(data => {
                alert(data.message || (data.success ? 'Updated' : 'Failed'));
                if (data.success) location.reload();
            })
            .catch(() => alert('Network error'));
        });
    });
});
</script>
</body>
</html>
