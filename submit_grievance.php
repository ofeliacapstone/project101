<?php
session_start();
require_once 'config.php'; // This will open $conn for the whole script

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

// Utilities: check for column/table existence
function db_has_column(mysqli $conn, string $table, string $column): bool {
    $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $conn->real_escape_string($table) . "' AND COLUMN_NAME = '" . $conn->real_escape_string($column) . "'";
    $res = @$conn->query($sql);
    if ($res && ($row = $res->fetch_row())) { return (int)$row[0] > 0; }
    return false;
}
function db_table_exists(mysqli $conn, string $table): bool {
    $sql = "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $conn->real_escape_string($table) . "'";
    $res = @$conn->query($sql);
    if ($res && ($row = $res->fetch_row())) { return (int)$row[0] > 0; }
    return false;
}

// Load categories if table exists
$categories = [];
$catRes = @$conn->query("SELECT id, name FROM grievance_categories ORDER BY name");
if ($catRes) { while ($r = $catRes->fetch_assoc()) { $categories[] = $r; } }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validate
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "❌ Invalid request. Please refresh and try again.";
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = isset($_POST['category_id']) && ctype_digit((string)$_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $severity = $_POST['severity'] ?? 'low';
        $allowedSev = ['low','medium','high','critical'];
        if (!in_array($severity, $allowedSev, true)) { $severity = 'low'; }
        $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

        if ($title === '' || strlen($title) < 5 || $description === '' || strlen($description) < 20) {
            $message = "⚠️ Please provide a valid title (min 5 chars) and description (min 20 chars).";
        } else {
            $hasCategoryIdCol = db_has_column($conn, 'grievances', 'category_id');
            $hasSeverityCol = db_has_column($conn, 'grievances', 'severity');
            $hasAnonymousCol = db_has_column($conn, 'grievances', 'is_anonymous');
            $hasAttachmentCol = db_has_column($conn, 'grievances', 'attachment');

            $grievanceId = null;

            if ($hasCategoryIdCol && $hasSeverityCol && $hasAnonymousCol) {
                $stmt = $conn->prepare("INSERT INTO grievances (user_id, title, description, submission_date, status, category_id, severity, is_anonymous) VALUES (?, ?, ?, NOW(), 'pending', ?, ?, ?)");
                $stmt->bind_param("sssisi", $user_id, $title, $description, $category_id, $severity, $is_anonymous);
            } else {
                $stmt = $conn->prepare("INSERT INTO grievances (user_id, title, description, submission_date, status) VALUES (?, ?, ?, NOW(), 'pending')");
                $stmt->bind_param("sss", $user_id, $title, $description);
            }

            if ($stmt && $stmt->execute()) {
                $grievanceId = $stmt->insert_id;
                $message = "✅ Grievance submitted successfully.";
            } else {
                $message = "❌ Error submitting grievance: " . ($stmt ? $stmt->error : $conn->error);
            }

            if ($stmt) $stmt->close();

            // Handle attachments if any
            if ($grievanceId && isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
                $uploadDir = __DIR__ . '/uploads/grievances';
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                $saved = [];
                $count = count($_FILES['attachments']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $error = $_FILES['attachments']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                    if ($error !== UPLOAD_ERR_OK) { continue; }
                    $tmp = $_FILES['attachments']['tmp_name'][$i];
                    $orig = basename($_FILES['attachments']['name'][$i]);
                    $size = (int)($_FILES['attachments']['size'][$i] ?? 0);
                    $type = $_FILES['attachments']['type'][$i] ?? '';
                    if ($size > 10 * 1024 * 1024) { continue; } // 10MB limit
                    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                    $safeName = 'grv_' . $grievanceId . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $orig);
                    $dest = $uploadDir . '/' . $safeName;
                    if (@move_uploaded_file($tmp, $dest)) {
                        $relPath = 'uploads/grievances/' . $safeName;
                        $saved[] = ['path' => $relPath, 'type' => $type, 'size' => $size];
                    }
                }
                // Save first file to grievances.attachment if column exists
                if ($hasAttachmentCol && !empty($saved)) {
                    $first = $saved[0]['path'];
                    $u = $conn->prepare("UPDATE grievances SET attachment = ? WHERE id = ?");
                    $u->bind_param("si", $first, $grievanceId);
                    @$u->execute();
                    $u->close();
                }
                // Save to grievance_attachments table if available
                if (db_table_exists($conn, 'grievance_attachments') && !empty($saved)) {
                    foreach ($saved as $f) {
                        $ai = $conn->prepare("INSERT INTO grievance_attachments (grievance_id, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                        $ai->bind_param("issis", $grievanceId, $f['path'], $f['type'], $f['size'], $user_id);
                        @$ai->execute();
                        $ai->close();
                    }
                }
            }
        }
    }
}

// DO NOT close $conn here, so student_header.php can use it!
// $conn->close() should only be done at the very end of the script, after all includes.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Grievance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container {
            max-width: 900px; margin: 50px auto; padding: 20px;
            background-color: white; border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; color: #003366; font-weight: 700;}
        .form-label { font-weight: bold; }
        .form-control {
            width: 100%; padding: 10px; margin: 10px 0 20px;
            border: 1px solid #ccc; border-radius: 5px; font-size: 16px;
        }
        .row { display:flex; gap:16px; }
        .col { flex:1; }
        .btn-primary {
            background-color: #003366; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: gold; color: #003366;
        }
        .alert {
            padding: 15px;
            background-color: #33cc99;
            color: #003366;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align:center;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }
        .alert.error { background-color: #d9534f; color: white; }
        .hint { color:#666; font-size: 0.9rem; margin-top:-10px; }
    </style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="container">
    <h2 class="text-center mt-4"><i class="fa-solid fa-file-circle-exclamation"></i> Submit Grievance</h2>
    <?php if (isset($message)): ?>
        <div class="alert<?= (strpos($message,'Error')!==false || strpos($message,'Invalid')!==false)?' error':'' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="submit_grievance.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required minlength="5" maxlength="120" placeholder="Brief title">
        </div>
        <div class="row">
            <div class="mb-3 col">
                <label for="category_id" class="form-label">Category</label>
                <?php if (!empty($categories)): ?>
                    <select class="form-control" id="category_id" name="category_id">
                        <option value="">Select category (optional)</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" class="form-control" placeholder="Category (optional)" disabled>
                    <div class="hint">Tip: Run migration at <code>migrate_grievances_web.php</code> to enable categories.</div>
                <?php endif; ?>
            </div>
            <div class="mb-3 col">
                <label for="severity" class="form-label">Severity</label>
                <select class="form-control" id="severity" name="severity">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required minlength="20" placeholder="Describe your grievance..."></textarea>
        </div>
        <div class="mb-3">
            <label for="attachments" class="form-label">Attachments</label>
            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept="image/*,application/pdf">
            <div class="hint">Max 10MB each. Images or PDF.</div>
        </div>
        <div class="mb-3">
            <label class="form-label"><input type="checkbox" name="is_anonymous" value="1"> Submit anonymously</label>
        </div>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit</button>
    </form>
</div>
<?php $conn->close(); // Now it's safe to close the connection ?>
</body>
</html>