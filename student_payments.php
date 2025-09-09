<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}
$studentId = $_SESSION['user_id'];
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Fetch full name
$stmt = $conn->prepare("SELECT TRIM(CONCAT(first_name,' ',COALESCE(NULLIF(middle_name,''),''),' ',last_name)) AS full_name FROM users WHERE user_id = ?");
$stmt->bind_param('s', $studentId);
$stmt->execute();
$fullName = ($stmt->get_result()->fetch_assoc()['full_name']) ?? '';
// Fetch current dorm room assignment
$roomId = null; $roomName = '';
$stmt = $conn->prepare("SELECT r.id, r.name FROM student_room_assignments a JOIN rooms r ON a.room_id = r.id WHERE a.user_id = ? AND a.status = 'Active' ORDER BY a.assigned_at DESC LIMIT 1");
$stmt->bind_param('s', $studentId);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
if ($room) { $roomId = (int)$room['id']; $roomName = $room['name']; }
// Fallback: approved application
if (!$roomId) {
    $stmt = $conn->prepare("SELECT r.id, r.name FROM student_room_applications sa JOIN rooms r ON sa.room_id = r.id WHERE sa.user_id = ? AND sa.status = 'Approved' ORDER BY sa.applied_at DESC LIMIT 1");
    $stmt->bind_param('s', $studentId);
    $stmt->execute();
    $appRoom = $stmt->get_result()->fetch_assoc();
    if ($appRoom) { $roomId = (int)$appRoom['id']; $roomName = $appRoom['name']; }
}
// Payment history
$rows = [];
$stmt = $conn->prepare("SELECT p.id, p.room_id, r.name AS room_name, p.amount, p.receipt_path, p.status, p.submitted_at, p.remarks FROM payments p LEFT JOIN rooms r ON p.room_id = r.id WHERE p.student_id = ? ORDER BY p.submitted_at DESC");
$stmt->bind_param('s', $studentId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) { $rows[] = $r; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #3572b7;
            --secondary: #f7f8fa;
            --accent: #e3f0ff;
            --card-shadow: 0 8px 32px rgba(53,114,183,0.09);
            --radius: 1.2rem;
            --transition: 0.15s cubic-bezier(.4,0,.2,1);
        }

        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f7f8fa 100%);
            font-family: 'Roboto', 'Segoe UI', Arial, sans-serif;
            color: #222;
        }
        .container-xl {
            max-width: 900px;
        }
        h2, h4 {
            font-weight: 700;
            letter-spacing: .02em;
        }
        .card {
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            border: none;
        }
        .card .card-header {
            background: var(--primary);
            color: #fff;
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 1rem 1.5rem;
            font-size: 1.32rem;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            transition: background var(--transition), box-shadow var(--transition);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: #285a8c;
            box-shadow: 0 2px 12px rgba(53,114,183,0.17);
        }
        .btn-outline-secondary {
            border-radius: 2em;
        }
        .progress { height: 10px; border-radius: 7px; }
        .progress-bar {
            transition: width .3s;
        }
        .table {
            border-radius: var(--radius);
            background: #fff;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-badge {
            padding: .4em 1.1em;
            font-size: .93em;
            font-weight: 500;
            border-radius: 2em;
            letter-spacing: .03em;
            border: none;
            transition: background var(--transition);
        }
        .status-pending { background: #ffe69c; color: #8d6700; }
        .status-success { background: #c6f6d5; color: #1d7e39; }
        .status-danger  { background: #f8d7da; color: #a94442; }
        .status-other   { background: #e3f0ff; color: #3572b7; }
        .receipt-link {
            transition: box-shadow var(--transition), transform var(--transition);
        }
        .receipt-link:hover {
            box-shadow: 0 1px 6px #3572b740;
            background: var(--accent);
            transform: scale(1.04);
        }
        .remarks {
            font-size: .95em;
            color: #6c757d;
        }
        /* Modal */
        .modal-lg { max-width: 720px; }
        .receipt-preview {
            display: block;
            width: 100%;
            height: auto;
            border-radius: .5em;
            box-shadow: 0 2px 10px #3572b729;
        }
        @media (max-width: 575.98px) {
            .container-xl { max-width: 100%; }
            .table th, .table td { font-size: .97em; }
            .card .card-header { font-size: 1.1em; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="container-xl mx-auto py-4">
    <h2 class="mb-4 text-center text-primary"><i class="bi bi-credit-card"></i> Dormitory Payments</h2>
    <div class="card mb-4">
        <div class="card-header">
            <span><i class="bi bi-upload"></i> Upload New Payment</span>
        </div>
        <div class="card-body">
            <form id="uploadForm" method="post" enctype="multipart/form-data" action="upload_payment.php" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label">Student ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($studentId) ?>" disabled>
                    </div>
                    <div class="col-md-4 col-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($fullName) ?>" disabled>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label">Dormitory Room</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($roomName ?: 'Not Assigned') ?>" disabled>
                        <input type="hidden" name="room_id" value="<?= (int)($roomId ?: 0) ?>">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="₱0.00" required>
                    </div>
                </div>
                <div class="row g-3 mt-2 align-items-end">
                    <div class="col-md-8 col-12">
                        <label class="form-label">Receipt <span class="text-muted">(JPG, PNG, PDF)</span></label>
                        <input type="file" name="receipt" id="receipt" class="form-control" accept="image/jpeg,image/png,application/pdf" required>
                    </div>
                    <div class="col-md-4 col-12 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-upload"></i> Upload Payment
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress d-none" id="progressBar">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <h4 class="mb-3 fw-semibold"><i class="bi bi-clock-history"></i> Payment History</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Room</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2"></i>
                        <div>No payment records yet.</div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($rows as $p): ?>
                <tr>
                    <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($p['submitted_at']))) ?></td>
                    <td><?= htmlspecialchars($p['room_name'] ?: ('Room #' . (int)$p['room_id'])) ?></td>
                    <td class="fw-semibold text-primary">₱<?= htmlspecialchars(number_format((float)$p['amount'],2)) ?></td>
                    <td>
                        <?php
                            $badgeClass = ($p['status']=='Verified') ? 'status-success' :
                                         (($p['status']=='Rejected') ? 'status-danger' :
                                         (($p['status']=='Pending') ? 'status-pending' : 'status-other'));
                        ?>
                        <span class="status-badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($p['receipt_path'])): ?>
                        <a href="#" class="btn btn-sm btn-outline-secondary receipt-link" 
                           data-bs-toggle="modal"
                           data-bs-target="#receiptModal"
                           data-id="<?= (int)$p['id'] ?>"
                           data-path="<?= htmlspecialchars($p['receipt_path']) ?>"
                           data-token="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <i class="bi bi-file-earmark-text"></i> View
                        </a>
                        <?php else: ?>
                        <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'Rejected' && !empty($p['remarks'])): ?>
                            <span class="text-danger remarks"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($p['remarks']) ?></span>
                        <?php elseif (!empty($p['remarks'])): ?>
                            <span class="text-secondary remarks"><?= htmlspecialchars($p['remarks']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Receipt Preview Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="receiptModalLabel"><i class="bi bi-file-earmark-text"></i> Receipt Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="receiptModalBody">
                    <div class="spinner-border text-primary my-4" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('uploadForm').addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const file = document.getElementById('receipt').files[0];
    if (!file) { alert('Please select a file'); return; }
    if (!['image/jpeg','image/png','application/pdf'].includes(file.type)) { alert('Invalid file type'); return; }
    if (file.size > 5 * 1024 * 1024) { alert('Max file size is 5MB'); return; }
    const bar = document.getElementById('progressBar');
    bar.classList.remove('d-none');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.upload.onprogress = function(evt){
        if (evt.lengthComputable) {
            const p = Math.floor((evt.loaded/evt.total)*100);
            bar.firstElementChild.style.width = p + '%';
        }
    };
    xhr.onload = function(){
        try {
            const res = JSON.parse(xhr.responseText);
            alert(res.message || (res.success?'Uploaded':'Failed'));
            if (res.success) location.reload();
        }
        catch(e){ alert('Server error'); }
    };
    const data = new FormData(form);
    xhr.send(data);
});
// Modal receipt preview
const receiptModal = document.getElementById('receiptModal');
receiptModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const paymentId = button.getAttribute('data-id');
    const csrfToken = button.getAttribute('data-token');
    const modalBody = document.getElementById('receiptModalBody');
    modalBody.innerHTML = '<div class="spinner-border text-primary my-4" role="status"><span class="visually-hidden">Loading...</span></div>';
    // Fetch receipt file (show image/pdf preview)
    fetch(`view_payment_file.php?id=${paymentId}&csrf_token=${csrfToken}`)
        .then(resp => resp.blob())
        .then(blob => {
            const mimeType = blob.type;
            let content = '';
            if (mimeType.startsWith('image/')) {
                content = `<img src="${URL.createObjectURL(blob)}" alt="Receipt" class="receipt-preview"/>`;
            } else if (mimeType === 'application/pdf') {
                content = `<embed src="${URL.createObjectURL(blob)}" type="application/pdf" width="100%" height="500px" class="receipt-preview"/>`;
            } else {
                content = '<div class="text-danger">Unable to preview this file type.</div>';
            }
            modalBody.innerHTML = content;
        })
        .catch(() => {
            modalBody.innerHTML = '<div class="text-danger">Failed to load receipt preview.</div>';
        });
});
</script>
</body>
</html>