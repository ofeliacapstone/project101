<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = trim($_POST['period'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $receiptNumber = trim($_POST['receipt_number'] ?? '');
    $datePaid = trim($_POST['date_paid'] ?? '');

    if ($period === '' || $amount === '' || $receiptNumber === '' || $datePaid === '') {
        $error = 'Please complete all required fields.';
    } elseif (!isset($_FILES['receipt_file']) || $_FILES['receipt_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid receipt file.';
    } else {
        // File validations
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['receipt_file']['size'] > $maxSize) {
            $error = 'File too large. Max 5MB allowed.';
        }

        $allowedMime = ['image/jpeg','image/png','application/pdf'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['receipt_file']['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            $error = 'Invalid file type. Only JPG, PNG, or PDF allowed.';
        }

        if ($error === '') {
            $ext = 'bin';
            if ($mime === 'image/jpeg') { $ext = 'jpg'; }
            elseif ($mime === 'image/png') { $ext = 'png'; }
            elseif ($mime === 'application/pdf') { $ext = 'pdf'; }

            $uploadDir = __DIR__ . '/uploads/payments';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $hashedName = bin2hex(random_bytes(16)) . '.' . $ext;
            $destPath = $uploadDir . '/' . $hashedName;

            if (!move_uploaded_file($_FILES['receipt_file']['tmp_name'], $destPath)) {
                $error = 'Failed to save uploaded file.';
            } else {
                // Insert record
                $stmt = $conn->prepare("INSERT INTO dormitory_payments (user_id, period, amount, receipt_number, date_paid, receipt_file, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
                $stmt->bind_param('ssdsis', $userId, $period, $amount, $receiptNumber, $datePaid, $hashedName);
                if ($stmt->execute()) {
                    $success = 'Payment proof submitted. Awaiting verification.';
                } else {
                    $error = 'Failed to save payment submission.';
                }
            }
        }
    }
}

// Fetch history
$history = [];
$stmt = $conn->prepare("SELECT id, period, amount, receipt_number, date_paid, receipt_file, status, remarks, created_at FROM dormitory_payments WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('s', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $history[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Payment Proof</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .preview { max-width: 320px; max-height: 320px; display: none; margin-top: 10px; }
    </style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="container mt-4">
    <h2>Submit Payment Proof</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="card mb-4">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Payment Month/Period</label>
                        <input type="text" name="period" class="form-control" placeholder="e.g., April 2025" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date Paid</label>
                        <input type="date" name="date_paid" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Upload Receipt (JPG, PNG, PDF)</label>
                        <input type="file" name="receipt_file" id="receipt_file" class="form-control" accept="image/jpeg,image/png,application/pdf" required>
                        <img id="previewImg" class="preview" alt="Preview">
                        <div id="previewPdf" class="mt-2" style="display:none;"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <h4>Your Payment History</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Amount</th>
                    <th>Receipt #</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['period']) ?></td>
                    <td>₱<?= htmlspecialchars(number_format((float)$p['amount'],2)) ?></td>
                    <td><?= htmlspecialchars($p['receipt_number']) ?></td>
                    <td><?= htmlspecialchars($p['date_paid']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td><?= htmlspecialchars($p['remarks'] ?? '') ?></td>
                    <td><a class="btn btn-sm btn-outline-secondary" href="view_payment_receipt.php?id=<?= (int)$p['id'] ?>" target="_blank">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
const fileInput = document.getElementById('receipt_file');
const previewImg = document.getElementById('previewImg');
const previewPdf = document.getElementById('previewPdf');
fileInput.addEventListener('change', function(){
    previewImg.style.display = 'none';
    previewPdf.style.display = 'none';
    previewPdf.innerHTML = '';
    if (!this.files || !this.files[0]) return;
    const file = this.files[0];
    if (file.type === 'image/jpeg' || file.type === 'image/png') {
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; previewImg.style.display = 'block'; };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        previewPdf.textContent = 'Selected PDF: ' + file.name;
        previewPdf.style.display = 'block';
    }
});
</script>
</body>
</html>
