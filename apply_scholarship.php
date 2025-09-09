<?php
session_start();
require_once 'config.php';
require_once 'csrf.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'User not authenticated']); exit(); }

try {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) throw new Exception('Invalid CSRF token.');
    if (!isset($_POST['scholarship_id'], $_POST['family_income'])) throw new Exception('Missing required application information.');
    $scholarship_id = (int)$_POST['scholarship_id'];
    $user_id = $_SESSION['user_id'];
    // GPA removed per requirement; ignore if provided
    $gpa = null;
    $family_income = (float)$_POST['family_income'];
    // GPA range validation removed. GPA is not required for application eligibility.
    $ss = $conn->prepare("SELECT * FROM scholarships WHERE id=? AND status='active'");
    $ss->bind_param("i", $scholarship_id);
    $ss->execute(); $res = $ss->get_result();
    if (!$res->num_rows) throw new Exception('Scholarship not found or inactive.');
    $sch = $res->fetch_assoc();
    $ss->close();
    // Only enforce deadline if it is set and valid (inclusive of the deadline date)
    if (!empty($sch['deadline']) && $sch['deadline'] !== '0000-00-00') {
        $dt = DateTime::createFromFormat('Y-m-d', $sch['deadline']);
        $isValid = $dt && $dt->format('Y-m-d') === $sch['deadline'];
        if ($isValid) {
            $deadlineDate = $dt->format('Y-m-d');
            $todayDate = date('Y-m-d');
            if ($deadlineDate < $todayDate) throw new Exception('Application deadline has passed.');
        }
    }
    $chk = $conn->prepare("SELECT id FROM scholarship_applications WHERE scholarship_id=? AND user_id=?");
    $chk->bind_param("is", $scholarship_id, $user_id);
    $chk->execute(); if ($chk->get_result()->num_rows) throw new Exception('You have already applied for this scholarship.'); $chk->close();
    if ((int)$sch['max_applicants'] > 0) {
        $cc = $conn->prepare("SELECT COUNT(*) c FROM scholarship_applications WHERE scholarship_id=?");
        $cc->bind_param("i", $scholarship_id);
        $cc->execute(); $cur = (int)($cc->get_result()->fetch_assoc()['c'] ?? 0); $cc->close();
        if ($cur >= (int)$sch['max_applicants']) throw new Exception('Maximum number of applicants reached for this scholarship.');
    }
    $uu = $conn->prepare("SELECT * FROM users WHERE user_id=?");
    $uu->bind_param("s", $user_id);
    $uu->execute(); $user = $uu->get_result()->fetch_assoc(); $uu->close();
    if (!$user) throw new Exception('User not found.');
    $conn->begin_transaction();
    $course = $user['course'] ?? '';
    $year_level_value = isset($user['year']) && is_numeric($user['year']) ? (int)$user['year'] : null;
    $empty_docs_json = '[]';
    $ins = $conn->prepare("INSERT INTO scholarship_applications (scholarship_id, user_id, application_date, status, gpa, course, year_level, documents_submitted, created_at, updated_at) VALUES (?, ?, NOW(), 'pending', NULL, ?, ?, ?, NOW(), NOW())");
    $ins->bind_param("issis", $scholarship_id, $user_id, $course, $year_level_value, $empty_docs_json);
    if (!$ins->execute()) throw new Exception('Failed to create application.');
    $application_id = $conn->insert_id;
    $ins->close();
    // Uploads
    $uploaded_documents = [];
    $dir = __DIR__.'/uploads/scholarship_documents/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    // Harden directory: prevent direct execution and listing
    $htaccess = $dir.'.htaccess';
    if (!file_exists($htaccess)) {
        @file_put_contents($htaccess, "Options -Indexes\n<FilesMatch \\.(php|phtml|php5|phar)$>\n  Require all denied\n</FilesMatch>\n");
    }
    $indexFile = $dir.'index.html';
    if (!file_exists($indexFile)) {
        @file_put_contents($indexFile, '<!doctype html><title>Forbidden</title>');
    }
    if (isset($_FILES['documents']) && is_array($_FILES['documents']['name'])) {
        $doc_types = $_POST['document_types'] ?? [];
        $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
        foreach ($_FILES['documents']['name'] as $i=>$filename) {
            if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['documents']['tmp_name'][$i];
                $size = (int)$_FILES['documents']['size'][$i];
                $reportedMime = $_FILES['documents']['type'][$i];
                $detectedMime = $reportedMime;
                if ($finfo) {
                    $detected = $finfo->file($tmp);
                    if ($detected) $detectedMime = $detected;
                } elseif (function_exists('mime_content_type')) {
                    $detected = @mime_content_type($tmp);
                    if ($detected) $detectedMime = $detected;
                }
                $dtype = $doc_types[$i] ?? 'Unknown';
                $allowed = ['application/pdf','image/jpeg','image/jpg','image/png','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!in_array($detectedMime, $allowed)) throw new Exception('Invalid file type for document: '.$dtype);
                if ($size > 5*1024*1024) throw new Exception('File too large: '.$dtype);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $safeExt = in_array($ext, ['pdf','jpg','jpeg','png','doc','docx']) ? $ext : 'bin';
                $random = bin2hex(random_bytes(8));
                $newname = $application_id.'_'.$i.'_'.$random.'.'.$safeExt;
                $path = $dir.$newname;
                if (!move_uploaded_file($tmp, $path)) throw new Exception('Failed to save document: '.$dtype);
                $relPath = 'uploads/scholarship_documents/'.$newname;
                $originalName = basename($filename);
                $di = $conn->prepare("INSERT INTO scholarship_documents (application_id, document_type, file_name, file_path, file_size, mime_type, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $di->bind_param("isssis", $application_id, $dtype, $originalName, $relPath, $size, $detectedMime);
                if (!$di->execute()) throw new Exception('Failed to save document record.');
                $di->close();
                $uploaded_documents[] = ['type'=>$dtype,'filename'=>$originalName,'path'=>$relPath];
            }
        }
    }
    $docs_json = json_encode($uploaded_documents);
    $up = $conn->prepare("UPDATE scholarship_applications SET documents_submitted=? WHERE id=?");
    $up->bind_param("si", $docs_json, $application_id); $up->execute(); $up->close();
    // Optional counter update (ignore errors if column not present)
    if ($inc = @$conn->prepare("UPDATE scholarships SET current_applicants=current_applicants+1 WHERE id=?")) {
        $inc->bind_param("i", $scholarship_id);
        @$inc->execute();
        $inc->close();
    }
    // Notification
    if ($nn = @$conn->prepare("INSERT INTO scholarship_notifications (user_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'success', 0, NOW())")) {
        $title = 'Application Submitted Successfully';
        $message = 'Your application for '.$sch['name'].' has been submitted successfully. Application ID: '.$application_id;
        $nn->bind_param("sss", $user_id, $title, $message);
        @$nn->execute();
        $nn->close();
    }
    // Audit log (new_values only)
    if ($al = @$conn->prepare("INSERT INTO scholarship_audit_log (action, table_name, record_id, old_values, new_values, user_id, ip_address, user_agent, created_at) VALUES ('CREATE','scholarship_applications', ?, NULL, ?, ?, ?, ?, NOW())")) {
        $new_values = json_encode(['scholarship_id'=>$scholarship_id,'user_id'=>$user_id,'documents_count'=>count($uploaded_documents)]);
        $ip = $_SERVER['REMOTE_ADDR'] ?? null; $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $al->bind_param("issss", $application_id, $new_values, $user_id, $ip, $ua);
        @$al->execute();
        $al->close();
    }
    $conn->commit();
    echo json_encode(['status'=>'success','message'=>'Your scholarship application has been submitted successfully! Application ID: '.$application_id,'application_id'=>$application_id,'documents_uploaded'=>count($uploaded_documents)]);
} catch (Exception $e) {
    if ($conn->errno) $conn->rollback();
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
