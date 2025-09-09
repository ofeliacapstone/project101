<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { echo '<div class="alert alert-danger">Unauthorized.</div>'; exit(); }
if (!isset($_GET['application_id'])) { echo '<div class="alert alert-danger">Missing application ID.</div>'; exit(); }
$applicationId = (int)$_GET['application_id'];
$stmt = $conn->prepare("SELECT id, document_type, file_name, file_path, file_size, mime_type, uploaded_at FROM scholarship_documents WHERE application_id=? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $applicationId);
$stmt->execute();
$res = $stmt->get_result();
if (!$res->num_rows) { echo '<div class="alert alert-info">No documents uploaded.</div>'; exit(); }
echo '<div class="row g-3">';
while ($row = $res->fetch_assoc()) {
  $docId = (int)$row['id'];
  $viewUrl = 'view_scholarship_document.php?id='.$docId;
  $sizeKb = round(((int)$row['file_size']) / 1024, 2);
  $mime = strtolower($row['mime_type'] ?? '');
  $isImage = str_starts_with($mime, 'image/');
  $isPdf = $mime === 'application/pdf';
  $isImage = (strpos($mime, 'image/') === 0);
  $isPdf = ($mime === 'application/pdf');

  echo '<div class="col-md-6">';
  echo '<div class="card shadow-sm">';
  echo '<div class="card-body">';
  echo '<div class="d-flex justify-content-between align-items-start">';
  echo '<div><strong>'.htmlspecialchars($row['document_type']?:'Document').'</strong><br>';
  echo '<small class="text-muted">'.htmlspecialchars($row['file_name']).' • '.date("M j, Y g:i A", strtotime($row['uploaded_at'])).' • '.$sizeKb.' KB</small></div>';
  echo '<div class="btn-group">';
  echo '<a href="'.htmlspecialchars($viewUrl).'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> View</a>';
  echo '<button class="btn btn-sm btn-outline-danger ms-1 delete-document" data-document-id="'.$docId.'"><i class="fas fa-trash"></i></button>';
  echo '</div></div>';
  echo '<div class="mt-3">';
  if ($isImage) {
    echo '<img src="'.htmlspecialchars($viewUrl).'" alt="preview" style="max-width:100%; max-height:280px; border:1px solid #e9ecef; border-radius:8px;" />';
  } elseif ($isPdf) {
    echo '<embed src="'.htmlspecialchars($viewUrl).'" type="application/pdf" width="100%" height="300px" />';
  } else {
    echo '<div class="alert alert-secondary py-2 mb-0"><i class="fas fa-file"></i> Preview not available. Use View to open.</div>';
  }
  echo '</div>';
  echo '</div></div></div>';
}
echo '</div>';
?>