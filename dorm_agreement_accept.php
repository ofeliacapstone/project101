<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authenticated."]);
    exit;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(["success" => false, "message" => "Only students can accept the agreement."]);
    exit;
}

require_once __DIR__ . '/includes/DormAgreementService.php';
$agreementService = new DormAgreementService($conn);
$active = $agreementService->getActiveAgreement();
if (!$active) {
    echo json_encode(["success" => false, "message" => "No active agreement found."]);
    exit;
}

try {
    $agreementService->recordAcceptance($_SESSION['user_id'], (int)$active['id']);
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'rooms';
    $redirectUrl = 'rooms.php';
    if ($redirect === 'rooms') {
        $redirectUrl = 'rooms.php';
    }
    echo json_encode(["success" => true, "redirect" => $redirectUrl]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Failed to record acceptance."]);
}
