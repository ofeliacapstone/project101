<?php
include 'config.php'; 
include 'csrf.php';
include 'mailer.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['user_id'];
// Fetch the student's appointments with counselor name if present
$query = "SELECT a.*, u.first_name AS counselor_first, u.last_name AS counselor_last
          FROM appointments a
          LEFT JOIN users u ON a.user_id = u.user_id
          WHERE a.student_id = ?
          ORDER BY a.appointment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
       
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#f6f9fc; }
        .main-content { margin-top: 100px; margin-left: 350px; padding: 24px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 12px 40px rgba(2,32,71,.12); padding:16px; max-width:1000px; margin:0 auto; }
        h2 { margin-bottom: 12px; color:#0f172a; text-align:left; }
        .toast { background:#d1e7dd; color:#0f5132; padding:12px 14px; border-radius:10px; box-shadow:0 8px 24px rgba(2,32,71,.08); margin-bottom:12px; }
        .toast.error { background:#f8d7da; color:#842029; }
        table { width:100%; border-collapse:collapse; }
        thead th { position:sticky; top:0; background:#f8fafc; color:#334155; font-weight:600; padding:12px; border-bottom:1px solid #e5e7eb; }
        tbody td { padding:12px; border-bottom:1px solid #eef2f7; color:#0f172a; }
        tr:hover td { background:#fcfcfd; }
        .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .bg-pending { background:#fff3cd; color:#8a6d3b; }
        .bg-approved { background:#d1e7dd; color:#0f5132; }
        .bg-completed { background:#cfe2ff; color:#084298; }
        .bg-rejected { background:#f8d7da; color:#842029; }
        .actions a, .actions button { text-decoration:none; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    <div class="main-content">
        <div class="card">
        <h2>My Appointments</h2>
        <?php if (isset($_GET['success'])): ?>
        
            <div class="toast"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date/Time</th>
                    <th>Counselor</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Admin Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                        <td><?= htmlspecialchars(trim(($row['counselor_first'] ?? '').' '.($row['counselor_last'] ?? '')) ?: '—') ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <?php $st=strtolower($row['status']); $cls=$st==='approved'?'bg-approved':($st==='completed'?'bg-completed':($st==='rejected'?'bg-rejected':($st==='cancelled'?'bg-rejected':'bg-pending'))); ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['admin_message']) ?></td>
                        
                        <td class="actions">
                            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                            <?php if (in_array(strtolower($row['status']), ['pending','approved'], true)): ?>
                                <form method="POST" action="cancel_guidance_request.php" onsubmit="return confirm('Cancel this appointment?');" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    
                                    <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:8px 12px; border-radius:10px; cursor:pointer; box-shadow:0 4px 10px rgba(220,53,69,.18);">Cancel</button>
                                </form>
                                <?php if (strtolower($row['status']) === 'approved'): ?>
                                <form method="POST" action="student_request_reschedule.php" onsubmit="return confirm('Send reschedule request?');" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    
                                    <input type="datetime-local" name="new_datetime" required style="padding:8px 10px; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 2px 8px rgba(2,32,71,.06);">
                                    <input type="text" name="note" placeholder="Optional note" style="padding:8px 10px; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 2px 8px rgba(2,32,71,.06);">
                                    <button type="submit" style="background:#0d6efd; color:#fff; border:none; padding:8px 12px; border-radius:10px; cursor:pointer; box-shadow:0 4px 10px rgba(13,110,253,.18);">Request Reschedule</button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (in_array(strtolower($row['status']), ['approved','completed'], true) && !empty($row['appointment_date'])): ?>
                                <?php
                                    $ics = ics_download_link((int)$row['id'], $row['appointment_date']);
                                    try {
                                        $dt = new DateTime($row['appointment_date']);
                                        $dtUtc = clone $dt; $dtUtc->setTimezone(new DateTimeZone('UTC'));
                                        $startUtc = $dtUtc->format('Ymd\THis\Z');
                                        $endUtc = (clone $dtUtc)->modify('+1 hour')->format('Ymd\THis\Z');
                                    } catch (Exception $e) {
                                        $startUtc = $endUtc = '';
                                    }
                                    $title = 'Guidance Appointment';
                                    $details = 'Reason: '.($row['reason'] ?? '');
                                    $gcal = $startUtc && $endUtc
                                      ? 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='.rawurlencode($title).'&dates='.$startUtc.'/'.$endUtc.'&details='.rawurlencode($details)
                                      : '';
                                ?>
                                
                                <a href="<?= htmlspecialchars($ics) ?>" title="Download .ics file for Outlook/Apple/Google" style="background:#0d6efd; color:#fff; padding:8px 12px; border-radius:10px; text-decoration:none; box-shadow:0 4px 10px rgba(13,110,253,.18);">Add to Calendar (.ics)</a>
                                <?php if ($gcal): ?>
                     
                                <a href="<?= htmlspecialchars($gcal) ?>" target="_blank" rel="noopener" style="background:#198754; color:#fff; padding:8px 12px; border-radius:10px; text-decoration:none; box-shadow:0 4px 10px rgba(25,135,84,.18);">Google Calendar</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!in_array(strtolower($row['status']), ['pending','approved'], true) && !(in_array(strtolower($row['status']), ['approved','completed'], true) && !empty($row['appointment_date']))): ?>
                                —
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>