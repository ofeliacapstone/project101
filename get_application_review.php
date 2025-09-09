<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { echo '<div class="alert alert-danger">Unauthorized.</div>'; exit(); }
if (!isset($_GET['application_id'])) { echo '<div class="alert alert-danger">Missing application ID.</div>'; exit(); }
$applicationId = (int)$_GET['application_id'];

// Fetch application details
$sql = "SELECT sa.id, sa.user_id, sa.status, sa.application_date, s.name AS scholarship_name, s.documents_required,
               u.first_name, u.middle_name, u.last_name, u.email, u.phone, u.course, u.`year` AS year_level, u.section
        FROM scholarship_applications sa
        JOIN scholarships s ON sa.scholarship_id = s.id
        JOIN users u ON sa.user_id = u.user_id
        WHERE sa.id=?";
$st = $conn->prepare($sql);
$st->bind_param("i", $applicationId);
$st->execute();
$app = $st->get_result()->fetch_assoc();
$st->close();
if (!$app) { echo '<div class="alert alert-danger">Application not found.</div>'; exit(); }

$docs = [];
$ds = $conn->prepare("SELECT id, document_type, file_name, file_path, file_size, mime_type, uploaded_at FROM scholarship_documents WHERE application_id=? ORDER BY uploaded_at ASC");
$ds->bind_param("i", $applicationId);
$ds->execute();
$r = $ds->get_result();
while ($row = $r->fetch_assoc()) $docs[] = $row;
$ds->close();

$requiredDocuments = [];
if (!empty($app['documents_required'])) {
    $tmp = json_decode($app['documents_required'], true);
    if (is_array($tmp)) $requiredDocuments = $tmp;
}
?>
<div class="container-fluid">
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header">Applicant</div>
        <div class="card-body">
          <div><strong><?= htmlspecialchars(trim(($app['first_name'] ?? '').' '.(($app['middle_name'] ?? '')?($app['middle_name'].' '):'').($app['last_name'] ?? ''))) ?></strong></div>
          <div class="text-muted mb-2"><?= htmlspecialchars($app['user_id'] ?? '') ?></div>
          <div><i class="fa fa-envelope"></i> <?= htmlspecialchars($app['email'] ?? '') ?></div>
          <div><i class="fa fa-phone"></i> <?= htmlspecialchars($app['phone'] ?? '') ?></div>
          <div><i class="fa fa-book"></i> <?= htmlspecialchars($app['course'] ?? '') ?> • Year <?= htmlspecialchars((string)($app['year_level'] ?? '')) ?></div>
          <div class="mt-2"><i class="fa fa-calendar"></i> Applied: <?= date('M j, Y g:i A', strtotime($app['application_date'])) ?></div>
        </div>
      </div>
      <?php if ($requiredDocuments): ?>
      <div class="card shadow-sm mt-3">
        <div class="card-header">Checklist</div>
        <div class="card-body">
          <?php foreach ($requiredDocuments as $rd): $has = array_filter($docs, function($d) use ($rd){ return strtolower($d['document_type'] ?? '') === strtolower($rd); }); ?>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" <?= $has ? 'checked' : '' ?> disabled>
            <label class="form-check-label"><?= htmlspecialchars($rd) ?></label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <div class="card shadow-sm mt-3">
        <div class="card-header">Notes</div>
        <div class="card-body">
          <textarea class="form-control" rows="4" placeholder="Internal notes (not saved in this demo)"></textarea>
        </div>
      </div>
      <div class="d-grid gap-2 mt-3">
        <button class="btn btn-success do-approve" data-id="<?= (int)$applicationId ?>"><i class="fa fa-check"></i> Approve</button>
        <button class="btn btn-danger do-reject" data-id="<?= (int)$applicationId ?>" data-bs-toggle="modal" data-bs-target="#rejectReasonModal"><i class="fa fa-times"></i> Reject</button>
        <button class="btn btn-secondary do-pending" data-id="<?= (int)$applicationId ?>"><i class="fa fa-rotate-left"></i> Set Pending</button>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header">Documents</div>
        <div class="card-body">
          <?php if (!$docs): ?>
            <div class="alert alert-info">No documents uploaded.</div>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($docs as $d): $viewUrl = 'view_scholarship_document.php?id='.(int)$d['id']; $mime = strtolower($d['mime_type'] ?? ''); $isImage = (strpos($mime,'image/')===0); $isPdf = ($mime==='application/pdf'); ?>
              <div class="col-md-6">
                <div class="border rounded p-2 h-100">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <strong><?= htmlspecialchars($d['document_type'] ?: 'Document') ?></strong>
                      <div class="text-muted small"><?= htmlspecialchars($d['file_name']) ?> • <?= date('M j, Y g:i A', strtotime($d['uploaded_at'])) ?></div>
                    </div>
                    <div class="btn-group">
                      <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= htmlspecialchars($viewUrl) ?>"><i class="fa fa-eye"></i> View</a>
                      <button class="btn btn-sm btn-outline-secondary open-lightbox" data-view-src="<?= htmlspecialchars($viewUrl) ?>" data-type="<?= $isPdf ? 'pdf' : 'img' ?>">Lightbox</button>
                    </div>
                  </div>
                  <div class="mt-2">
                    <?php if ($isImage): ?>
                      <img src="<?= htmlspecialchars($viewUrl) ?>" class="img-fluid rounded doc-thumb" data-view-src="<?= htmlspecialchars($viewUrl) ?>" style="cursor:pointer; max-height:280px; object-fit:contain;" />
                    <?php elseif ($isPdf): ?>
                      <embed src="<?= htmlspecialchars($viewUrl) ?>" type="application/pdf" width="100%" height="280px" />
                    <?php else: ?>
                      <div class="alert alert-secondary py-2 mb-0">Preview not available.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
