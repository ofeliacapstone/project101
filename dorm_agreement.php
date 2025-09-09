<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

require_once __DIR__ . '/includes/DormAgreementService.php';
$agreementService = new DormAgreementService($conn);
$activeAgreement = $agreementService->getActiveAgreement();
$redirect = isset($_GET['redirect']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['redirect']) : 'rooms';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory Agreement & Policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .agreement-container { max-width: 900px; margin: 30px auto; }
        .content-box { max-height: 420px; overflow-y: auto; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; background-color: #ffffff; }
        .actions { display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
    </style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="agreement-container">
    <h2 class="text-center mb-3">Dormitory Agreement & Policy</h2>
    <?php if (!$activeAgreement): ?>
        <div class="alert alert-warning">No active agreement found. Please contact the Dormitory Office.</div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><?= htmlspecialchars($activeAgreement['title']) ?></strong>
                <a class="btn btn-outline-secondary btn-sm" href="download_dorm_agreement.php?id=<?= (int)$activeAgreement['id'] ?>">Download PDF</a>
            </div>
            <div class="card-body">
                <div class="content-box" id="agreementContent">
                    <?= nl2br(htmlspecialchars($activeAgreement['content'])) ?>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="agreeCheckbox">
                    <label class="form-check-label" for="agreeCheckbox">
                        I have read and agree to the Dormitory Agreement & Policy
                    </label>
                </div>
                <div class="actions mt-3">
                    <button id="agreeBtn" class="btn btn-primary" disabled>I Agree</button>
                    <a href="rooms.php" class="btn btn-link">Back to Rooms</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('agreeCheckbox');
        const agreeBtn = document.getElementById('agreeBtn');
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                agreeBtn.disabled = !this.checked;
            });
        }
        if (agreeBtn) {
            agreeBtn.addEventListener('click', function() {
                agreeBtn.disabled = true;
                fetch('dorm_agreement_accept.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'accept', redirect: '<?= htmlspecialchars($redirect) ?>' })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect || 'rooms.php';
                    } else {
                        alert(data.message || 'Failed to record acceptance.');
                        agreeBtn.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Network error. Please try again.');
                    agreeBtn.disabled = false;
                });
            });
        }
    });
</script>
</body>
</html>