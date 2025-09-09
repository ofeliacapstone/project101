<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

function csrf_token(): string {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool {
	if (!$token || empty($_SESSION['csrf_token'])) return false;
	return hash_equals($_SESSION['csrf_token'], $token);
}