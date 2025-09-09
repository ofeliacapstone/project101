<?php
include 'config.php';
include 'csrf.php';
include 'mailer.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
// Restrict to admins who manage guidance or platform
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Power Admin','Guidance Admin'], true)) {
    header('Location: login.php');
    exit;
}
$result_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test'])) {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Invalid CSRF token'); }
    $to = trim($_POST['to'] ?? '');
    $subject = trim($_POST['subject'] ?? 'SMTP Test');
    $message = trim($_POST['message'] ?? 'This is a test email from NEUST Guidance Services.');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $result_message = 'Please enter a valid email address.';
    } else {
        $content = '<p>'.$message.'</p>';
        $ok = @send_branded_email($to, $subject, 'SMTP Test', $content);
        $detail = $GLOBALS['SMTP_LAST_ERROR'] ?? '';
        $result_message = $ok ? 'Test email sent. Please check the inbox.' : ('Failed to send email. Check SMTP settings. '.($detail?('Details: '.$detail):''));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; background:#f6f9fc; margin:0; }
        .wrap { max-width:640px; margin:40px auto; background:#fff; border-radius:10px; box-shadow:0 6px 24px rgba(2,32,71,.07); overflow:hidden; }
        .head { background:#003366; color:#FFD700; padding:16px 20px; font-weight:700; font-size:18px; }
        .body { padding:20px; }
        .row { margin-bottom:12px; }
        label { display:block; margin-bottom:6px; color:#334155; font-weight:600; }
        input[type=text], textarea { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; }
        textarea { min-height:120px; resize:vertical; }
        .btn { background:#0d6efd; color:#fff; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; }
        .btn:hover { background:#0b5ed7; }
        .meta { margin-top:12px; color:#64748b; font-size:12px; }
        .msg-ok { color:#0f5132; background:#d1e7dd; padding:10px; border-radius:8px; margin-bottom:12px; }
        .msg-err { color:#842029; background:#f8d7da; padding:10px; border-radius:8px; margin-bottom:12px; }
    </style>
<?php /* Simple page without sidebars to keep isolated */ ?>
</head>
<body>
    <div class="wrap">
        <div class="head">SMTP Test</div>
        <div class="body">
            <?php if ($result_message): ?>
                <?php $ok = strpos($result_message, 'sent') !== false; ?>
                <div class="<?= $ok ? 'msg-ok' : 'msg-err' ?>"><?= htmlspecialchars($result_message) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <div class="row">
                    <label for="to">Recipient Email</label>
                    <input type="text" id="to" name="to" placeholder="you@example.com" required>
                </div>
                <div class="row">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="SMTP Test">
                </div>
                <div class="row">
                    <label for="message">Message</label>
                    <textarea id="message" name="message">This is a test email from NEUST Guidance Services.</textarea>
                </div>
                <button class="btn" type="submit" name="send_test" value="1"><i class="fa-solid fa-paper-plane"></i> Send Test Email</button>
                <div class="meta">
                  
                    Using SMTP host: <?= htmlspecialchars(defined('SMTP_HOST') ? SMTP_HOST : (getenv('SMTP_HOST') ?: 'PHP mail() fallback')) ?>
                    | From: <?= htmlspecialchars(defined('SMTP_FROM') ? SMTP_FROM : (getenv('SMTP_FROM') ?: APP_EMAIL_FROM)) ?>
                    | Secure: <?= htmlspecialchars(defined('SMTP_SECURE') ? SMTP_SECURE : (getenv('SMTP_SECURE') ?: 'tls')) ?>
                    | Port: <?= htmlspecialchars(defined('SMTP_PORT') ? SMTP_PORT : (getenv('SMTP_PORT') ?: '587')) ?>
                </div>
            </form>
        </div>
    </div>
</body>
</html>