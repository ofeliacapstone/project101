<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Scholarship Admin','Admin'])) { http_response_code(403); echo 'Unauthorized'; exit(); }

if (!isset($_GET['application_id'])) { http_response_code(400); echo 'Missing application ID'; exit(); }
$applicationId = (int)$_GET['application_id'];

// Fetch documents for the application
$stmt = $conn->prepare("SELECT file_name, file_path FROM scholarship_documents WHERE application_id=? ORDER BY uploaded_at ASC");
$stmt->bind_param("i", $applicationId);
$stmt->execute();
$res = $stmt->get_result();
if (!$res->num_rows) { http_response_code(404); echo 'No documents'; exit(); }

$files = [];
while ($row = $res->fetch_assoc()) {
	$real = realpath(__DIR__ . '/' . $row['file_path']);
	$base = realpath(__DIR__ . '/uploads/scholarship_documents');
	if ($real && $base && strpos($real, $base) === 0 && is_file($real)) {
		$files[] = ['path'=>$real, 'name'=>$row['file_name'] ?: basename($real)];
	}
}
if (!count($files)) { http_response_code(404); echo 'Files missing'; exit(); }

// Create ZIP in temp
$zipFile = tempnam(sys_get_temp_dir(), 'appdocs_');
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::OVERWRITE) !== TRUE) { http_response_code(500); echo 'ZIP error'; exit(); }
foreach ($files as $i=>$f) {
	// Ensure unique names in zip
	$entry = ($i+1).'_'.preg_replace('/[^A-Za-z0-9._ -]/','_', $f['name']);
	$zip->addFile($f['path'], $entry);
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Length: '.filesize($zipFile));
header('Content-Disposition: attachment; filename="application_'.$applicationId.'_documents.zip"');
readfile($zipFile);
@unlink($zipFile);