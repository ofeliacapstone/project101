<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
	echo json_encode(array("success" => false, "message" => "Not authenticated."));
	exit;
}

require_once __DIR__ . '/includes/DormAgreementService.php';
$service = new DormAgreementService($conn);

// Get active agreement
$active = $service->getActiveAgreement();
if (!$active) {
	// No active policy: treat as accepted so Apply modal can proceed
	echo json_encode(array("success" => true, "hasActive" => false, "accepted" => true));
	exit;
}

// Check acceptance
$accepted = $service->hasUserAccepted($_SESSION['user_id'], (int)$active['id']);
if ($accepted) {
	echo json_encode(array("success" => true, "hasActive" => true, "accepted" => true));
	exit;
}

// Not accepted: return agreement content
echo json_encode(array(
	"success" => true,
	"hasActive" => true,
	"accepted" => false,
	"agreement" => array(
		"id" => (int)$active['id'],
		"title" => $active['title'],
		"content" => $active['content']
	)
));