<?php
session_start();
require_once 'config.php';
// Restrict to admins (adjust role names as needed)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) {
    header("Location: login.php");
    exit();
}
// Fetch approved scholarship applications with user details (matches your schema)
$sql = "
    SELECT 
        s.name AS scholarship_name, 
        u.first_name, 
        u.middle_name, 
        u.last_name, 
        u.email, 
        u.phone, 
        u.course, 
        u.`year` AS year_level,
        u.section, 
        sa.application_date, 
        sa.approval_date, 
        sa.id AS application_id
    FROM scholarship_applications sa
    JOIN scholarships s ON sa.scholarship_id = s.id
    JOIN users u ON sa.user_id = u.user_id
    WHERE sa.status = 'approved'
    ORDER BY sa.application_date DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Scholars</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --neust-blue: #003366;
            --neust-light-blue: #00509E;
            --neust-gold: #FFD700;
            --neust-white: #FFFFFF;
            --sidebar-width: 260px;
        }
        body { background: #f4f4f4; }
        /* Prevent overlap with the fixed sidebar in admin header (if present) */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        @media (max-width: 991.98px) {
            .main-content { margin-left: 0; }
        }
        .page-header {
            background: linear-gradient(135deg, var(--neust-blue), var(--neust-light-blue));
            color: #fff;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 18px;
        }
        .controls-row { margin: 12px 0 18px; }
        .table thead th { background: var(--neust-blue); color: #fff; vertical-align: middle; }
        .badge-year {
            background: var(--neust-gold);
            color: var(--neust-blue);
            font-weight: 600;
        }
        .table-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            padding: 10px;
        }
    </style>
</head>
<body>

<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) { include 'admin_scholarship_header.php'; } ?>
<?php if (file_exists(__DIR__ . '/admin_scholarship_header.php')) { include_once 'admin_scholarship_header.php'; } ?>

<div class="main-content">
  <div class="container-fluid">
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h4 class="mb-0"><i class="fa fa-check-circle me-2"></i>Approved Scholars</h4>
        <div class="d-flex gap-2">
            <a href="scholarship_admin_dashboard.php" class="btn btn-sm btn-light">Dashboard</a>
            <a href="admin_manage_scholarships.php" class="btn btn-sm btn-light">Scholarships</a>
        </div>
    </div>
    <div class="row controls-row g-2">
        <div class="col-12 col-md-5 col-lg-4">
            <input id="search-bar" type="text" class="form-control" placeholder="Search scholars, course, scholarship...">
        </div>
    </div>
    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0" id="scholars-table">
            <thead>
                <tr>
                    <th style="min-width:180px">Scholarship Name</th>
                    <th style="min-width:200px">Student Name</th>
                    <th style="min-width:200px">Email</th>
                    <th style="min-width:130px">Phone</th>
                    <th style="min-width:140px">Course</th>
                    <th style="min-width:80px">Year</th>
                    <th style="min-width:100px">Section</th>
                    <th style="min-width:160px">Application Date</th>
                    <th style="min-width:150px">Date Approved</th>
                    <th style="min-width:110px">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['scholarship_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . (($row['middle_name'] ?? '') ? ($row['middle_name'] . ' ') : '') . ($row['last_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['course'] ?? '') ?></td>
                        <td><span class="badge badge-year">&nbsp;<?= htmlspecialchars((string)($row['year_level'] ?? '')) ?>&nbsp;</span></td>
                        <td><?= htmlspecialchars($row['section'] ?? '') ?></td>
                        <td><?= !empty($row['application_date']) ? date("F j, Y", strtotime($row['application_date'])) : '' ?></td>
                        <td><?= !empty($row['approval_date']) ? date("F j, Y", strtotime($row['approval_date'])) : '' ?></td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-btn" title="Delete" data-id="<?= (int)$row['application_id'] ?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center text-muted py-4">No approved scholars found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Client-side search (simple)
    $('#search-bar').on('keyup', function () {
        const val = ($(this).val() || '').toLowerCase();
        $('#scholars-table tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });
    // Delete record (keeps your existing endpoint)
    $(document).on('click', '.delete-btn', function () {
        if (!confirm('Delete this approved application?')) return;
        const id = $(this).data('id');
        $.post('delete_application.php', { id }, function (resp) {
            if (resp === 'success') {
                location.reload();
            } else {
                alert('Error deleting the record.');
            }
        });
    });
</script>
</body>
</html>