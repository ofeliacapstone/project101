<?php
include 'config.php';
include 'csrf.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Guidance Admin') { header('Location: login.php'); exit; }

$conn->query("CREATE TABLE IF NOT EXISTS guidance_blackouts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_start DATE NOT NULL,
  date_end DATE NOT NULL,
  note TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX(date_start), INDEX(date_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf_token'] ?? null)) { $msg = 'Invalid CSRF token'; }
  else if (isset($_POST['add'])) {
    $ds = $_POST['date_start'] ?? '';
    $de = $_POST['date_end'] ?? '';
    $note = trim($_POST['note'] ?? '');
    if (!$ds || !$de) { $msg = 'Please select dates'; }
    else {
      $stmt = $conn->prepare('INSERT INTO guidance_blackouts (date_start, date_end, note) VALUES (?,?,?)');
      $stmt->bind_param('sss', $ds, $de, $note);
      $ok = $stmt->execute();
      $msg = $ok ? 'Blackout added' : 'Failed to add blackout';
    }
  } else if (isset($_POST['delete'])) {
    $id = (int)($_POST['id'] ?? 0);
    $conn->query('DELETE FROM guidance_blackouts WHERE id='.(int)$id);
    $msg = 'Blackout deleted';
  }
}

$rows = $conn->query('SELECT * FROM guidance_blackouts ORDER BY date_start DESC');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blackout Dates</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Inter','Segoe UI',Arial,sans-serif; background:#f6f9fc; display:flex; }
    .main-content { margin-left:260px; padding:24px; width:calc(100% - 260px); }
    h2 { color:#0f172a; }
    .card { background:#fff; border-radius:12px; box-shadow:0 8px 30px rgba(2,32,71,.08); padding:16px; margin-bottom:16px; }
    .row { display:flex; gap:12px; flex-wrap:wrap; }
    input, textarea { padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; }
    button { background:#0d6efd; color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer; }
    button:hover { background:#0b5ed7; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #eef2f7; text-align:left; }
    th { background:#f8fafc; color:#334155; }
    .danger { background:#dc3545; }
  </style>
</head>
<body>
  <?php include 'guidance_admin_header.php'; ?>
  <div class="main-content">
    <h2>Blackout Dates</h2>
    <?php if ($msg): ?><div class="card" style="color:#0f5132;background:#d1e7dd;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="row">
          <div>
            <label>Start</label><br>
            <input type="date" name="date_start" required>
          </div>
          <div>
            <label>End</label><br>
            <input type="date" name="date_end" required>
          </div>
          <div style="flex:1 1 auto;">
            <label>Note</label><br>
            <input type="text" name="note" placeholder="Holiday, maintenance, etc.">
          </div>
          <div>
            <label>&nbsp;</label><br>
            <button type="submit" name="add" value="1"><i class="fa-solid fa-plus"></i> Add</button>
          </div>
        </div>
      </form>
    </div>
    <div class="card">
      <table>
        <thead><tr><th>Dates</th><th>Note</th><th>Action</th></tr></thead>
        <tbody>
          <?php if ($rows && $rows->num_rows): while($r=$rows->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['date_start']) ?> to <?= htmlspecialchars($r['date_end']) ?></td>
              <td><?= htmlspecialchars($r['note']) ?></td>
              <td>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this blackout?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
                  <button class="danger" type="submit" name="delete" value="1"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="3">No blackouts yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
