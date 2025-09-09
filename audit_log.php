<?php
if (!function_exists('guidance_log_action')) {
    function guidance_log_action(mysqli $conn, int $appointmentId, string $actorId, string $action, string $details = ''): void {
        $conn->query("CREATE TABLE IF NOT EXISTS appointment_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NOT NULL,
            actor_id VARCHAR(64) NOT NULL,
            action VARCHAR(64) NOT NULL,
            details TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX(appointment_id), INDEX(actor_id), INDEX(action)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $stmt = $conn->prepare("INSERT INTO appointment_logs (appointment_id, actor_id, action, details) VALUES (?,?,?,?)");
        $stmt->bind_param('isss', $appointmentId, $actorId, $action, $details);
        $stmt->execute();
    }
}
?>