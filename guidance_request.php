<?php
include 'config.php';
session_start();
require_once 'mailer.php';
// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validate
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid request. Please refresh and try again.";
    } else if (!isset($_SESSION['user_id'])) {
        $error_message = "You must be logged in to submit a request.";
    } else {
        $student_id = $_SESSION['user_id']; // Use user_id as student_id
        $appointment_date_raw = trim($_POST['appointment_date'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        // Basic validation
        if ($appointment_date_raw === '' || $reason === '' || strlen($reason) < 10) {
            $error_message = "Please provide a valid date/time and a brief reason (min 10 characters).";
        } else {
            // Normalize datetime (from input type=datetime-local -> YYYY-MM-DDTHH:MM)
            // Normalize to MySQL DATETIME
            try {
                $dtIn = new DateTime($appointment_date_raw);
                $appointment_date = $dtIn->format('Y-m-d H:i:00');
                if ($dtIn < new DateTime()) {
                    $error_message = "Please select a future date/time.";
                    $appointment_date = null;
                }
            } catch (Exception $e) {
                $error_message = "Invalid date/time format.";
                $appointment_date = null;
            }
            // Blackout days hint on server
            if (!isset($error_message) && function_exists('guidance_is_blackout')) {
                include_once 'guidance_availability.php';
                if (guidance_is_blackout($conn, new DateTime($appointment_date))) {
                    $error_message = "Selected date falls on a blackout day. Please choose another date.";
                    $appointment_date = null;
                }
            }
            // Pick a guidance admin/counselor to assign
            $guidance_admin_id = null;
            $sel = $conn->prepare("SELECT user_id FROM users WHERE role = 'Guidance Admin' AND status = 'Active' LIMIT 1");
            if ($sel && $sel->execute()) {
                $res = $sel->get_result()->fetch_assoc();
                if ($res) { $guidance_admin_id = $res['user_id']; }
            }
            if (!$guidance_admin_id) {
                $error_message = $error_message ?? "No available counselor at the moment. Please try again later.";
                $appointment_date = null;
            }
            // Insert query (keep existing schema)
            $insertQuery = "INSERT INTO appointments (student_id, user_id, appointment_date, reason, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($insertQuery);
            if ($stmt && $appointment_date) {
                $stmt->bind_param("ssss", $student_id, $guidance_admin_id, $appointment_date, $reason);
                if ($stmt->execute()) {
                    $success_message = "Guidance request submitted successfully.";
                    // Notify student confirmation
                    $me = $conn->prepare("SELECT email, TRIM(CONCAT(first_name,' ',last_name)) AS name FROM users WHERE user_id=?");
                    $me->bind_param('s', $student_id);
                    $me->execute(); $meRes = $me->get_result()->fetch_assoc();
                    if ($meRes && !empty($meRes['email'])) {
                        $content = '<p>Hello '.htmlspecialchars($meRes['name'] ?? $student_id).',</p><p>Your guidance request has been received and is currently pending. You will be notified once a counselor schedules your appointment.</p>';
                        @send_branded_email($meRes['email'], 'Request Received', 'Request Received', $content);
                    }
                } else {
                    $error_message = "Submission failed. Please try again later.";
                }
            } else {
                $error_message = "Server error. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Guidance Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
    
        body { font-family:'Roboto',sans-serif; background:#f6f9fc; margin:0; }
        .main-content { display:flex; justify-content:center; align-items:center; min-height: calc(100vh - 56px); padding:24px; }
        .container { background:#fff; padding:24px; border-radius:12px; box-shadow:0 12px 40px rgba(2,32,71,.12); width:100%; max-width:560px; text-align:left; }
        h2 { margin-bottom:12px; color:#0f172a; }
        .toast { background:#d1e7dd; color:#0f5132; padding:12px 14px; border-radius:10px; box-shadow:0 8px 24px rgba(2,32,71,.08); margin-bottom:12px; }
        .toast.error { background:#f8d7da; color:#842029; }
        label { display:block; margin:10px 0 6px; color:#334155; font-weight:600; }
        input, textarea { width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:10px; font-size:16px; background:#fff; box-shadow:0 2px 8px rgba(2,32,71,.06); }
        input:focus, textarea:focus { outline:none; border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.15); }
        small.helper { display:block; color:#64748b; margin-top:6px; }
        .row { display:flex; gap:12px; align-items:center; }
        .btn { padding:12px 16px; background:#0d6efd; color:#fff; border:none; border-radius:10px; cursor:pointer; box-shadow:0 8px 20px rgba(13,110,253,.18); }
        .btn:hover { background:#0b5ed7; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    <div class="main-content">
        <div class="container">
            <h2>Submit Guidance Request</h2>
            <?php if (isset($success_message)): ?>
                
                <div class="toast"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="toast error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <label for="appointment_date">Appointment Date:</label>
                <input type="datetime-local" id="appointment_date" name="appointment_date" required>
                <script>
                (function(){
                    try{
                        const input = document.getElementById('appointment_date');
                        const now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        const iso = now.toISOString().slice(0,16);
                        input.min = iso;
                        input.addEventListener('change', function(){
                            const v = this.value;
                            if(!v) return;
                            const d = new Date(v);
                            const local = new Date(d.getTime() - (d.getTimezoneOffset()*60000));
                            const day = local.getDay(); // 0=Sun,1=Mon
                            const h = local.getHours();
                            if (day === 0 || day === 6 || h < 8 || h > 16) {
                                alert('Preferred time is outside business hours (Mon–Fri, 08:00–17:00). You can submit, but scheduling may adjust.');
                            }
                        });
                    }catch(e){}
                })();
                </script>
                <label for="reason">Reason:</label>
                <textarea id="reason" name="reason" required minlength="10" placeholder="Briefly describe your concern..."></textarea>
                <small class="helper">We will try to accommodate your preferred time and match you with an available counselor.</small>
                <button type="submit" class="btn">Submit Request</button>
            </form>
        </div>
    </div>
</body>
</html>