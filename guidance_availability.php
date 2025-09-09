<?php
function guidance_is_within_business_hours(DateTime $start): bool {
    // Business hours: Monday–Friday, 08:00 to 17:00. Appointments are 1 hour.
    // Require start time between 08:00 and 16:00 inclusive so it ends by 17:00.
    $day = (int)$start->format('N'); // 1 = Monday, 7 = Sunday
    if ($day < 1 || $day > 5) {
        return false;
    }
    $minutes = ((int)$start->format('H')) * 60 + (int)$start->format('i');
    $minStart = 8 * 60;   // 08:00
    $maxStart = 16 * 60;  // 16:00, so +1h ends at 17:00
    return $minutes >= $minStart && $minutes <= $maxStart;
}

function guidance_is_blackout(mysqli $conn, DateTime $start): bool {
    // Table: guidance_blackouts (date_start DATE, date_end DATE, note TEXT)
    $conn->query("CREATE TABLE IF NOT EXISTS guidance_blackouts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date_start DATE NOT NULL,
        date_end DATE NOT NULL,
        note TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX(date_start), INDEX(date_end)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $d = $start->format('Y-m-d');
    $stmt = $conn->prepare('SELECT id FROM guidance_blackouts WHERE ? BETWEEN date_start AND date_end LIMIT 1');
    $stmt->bind_param('s', $d);
    $stmt->execute();
    $res = $stmt->get_result();
    return (bool)$res->fetch_row();
}
?>