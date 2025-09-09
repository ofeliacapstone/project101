<?php
// Grievance lifecycle migration runner
// Safe to run multiple times

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . '/config.php';

function tableExists(mysqli $conn, string $tableName): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    $stmt->bind_param('s', $tableName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count > 0;
}

function columnExists(mysqli $conn, string $tableName, string $columnName): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param('ss', $tableName, $columnName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count > 0;
}

function indexExists(mysqli $conn, string $tableName, string $indexName): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?");
    $stmt->bind_param('ss', $tableName, $indexName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count > 0;
}

// 1) Create supporting tables if missing
$createTables = [
    'grievance_categories' => <<<SQL
        CREATE TABLE grievance_categories (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(120) NOT NULL,
          parent_id INT NULL,
          sla_first_response_hours INT DEFAULT 24,
          sla_resolve_hours INT DEFAULT 168,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX idx_parent (parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL,
    'grievance_comments' => <<<SQL
        CREATE TABLE grievance_comments (
          id INT AUTO_INCREMENT PRIMARY KEY,
          grievance_id INT NOT NULL,
          author_user_id INT NOT NULL,
          is_internal TINYINT(1) DEFAULT 0,
          body TEXT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_grievance (grievance_id),
          CONSTRAINT fk_comments_grievance FOREIGN KEY (grievance_id) REFERENCES grievances(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL,
    'grievance_attachments' => <<<SQL
        CREATE TABLE grievance_attachments (
          id INT AUTO_INCREMENT PRIMARY KEY,
          grievance_id INT NOT NULL,
          file_path VARCHAR(255) NOT NULL,
          file_type VARCHAR(100) NULL,
          file_size INT NULL,
          uploaded_by INT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_grievance (grievance_id),
          CONSTRAINT fk_attachments_grievance FOREIGN KEY (grievance_id) REFERENCES grievances(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL,
    'grievance_status_history' => <<<SQL
        CREATE TABLE grievance_status_history (
          id INT AUTO_INCREMENT PRIMARY KEY,
          grievance_id INT NOT NULL,
          from_status VARCHAR(50) NULL,
          to_status VARCHAR(50) NOT NULL,
          actor_user_id INT NOT NULL,
          reason TEXT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_grievance (grievance_id),
          CONSTRAINT fk_history_grievance FOREIGN KEY (grievance_id) REFERENCES grievances(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL,
];

foreach ($createTables as $table => $sql) {
    if (!tableExists($conn, $table)) {
        $conn->query($sql);
    }
}

// 2) Extend grievances table
$conn->query("ALTER TABLE grievances MODIFY COLUMN status ENUM('pending','acknowledged','info_requested','assigned','in_progress','resolved','rejected','escalated','closed','reopened','withdrawn') DEFAULT 'pending'");

$columns = [
    'category_id' => "INT NULL",
    'sub_category_id' => "INT NULL",
    'severity' => "ENUM('low','medium','high','critical') DEFAULT 'low'",
    'is_anonymous' => "TINYINT(1) DEFAULT 0",
    'assigned_to_user_id' => "INT NULL",
    'acknowledged_at' => "DATETIME NULL",
    'first_response_at' => "DATETIME NULL",
    'in_progress_at' => "DATETIME NULL",
    'closed_at' => "DATETIME NULL",
    'resolution_summary' => "TEXT NULL",
    'rejection_reason' => "TEXT NULL",
    'reopen_count' => "INT DEFAULT 0",
    'updated_at' => "TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
];

foreach ($columns as $name => $definition) {
    if (!columnExists($conn, 'grievances', $name)) {
        $conn->query("ALTER TABLE grievances ADD COLUMN $name $definition");
    }
}

// 3) Add helpful indexes
$indexes = [
    'idx_category' => 'category_id',
    'idx_status' => 'status',
    'idx_assigned' => 'assigned_to_user_id',
];
foreach ($indexes as $idxName => $col) {
    if (!indexExists($conn, 'grievances', $idxName)) {
        $conn->query("ALTER TABLE grievances ADD INDEX $idxName ($col)");
    }
}

// 4) Seed base categories
$seeds = ['Academics', 'Facilities', 'Finance'];
foreach ($seeds as $name) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM grievance_categories WHERE name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ((int)$count === 0) {
        $ins = $conn->prepare("INSERT INTO grievance_categories (name) VALUES (?)");
        $ins->bind_param('s', $name);
        $ins->execute();
        $ins->close();
    }
}

echo "Grievance migration completed.\n";
?>
