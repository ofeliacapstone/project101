<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
include_once 'config.php';
$pendingCount = 0;
try {
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Guidance Admin','Counselor'], true)) {
        if ($_SESSION['role'] === 'Guidance Admin') {
            $q = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status IN ('pending','Pending')");
            $pendingCount = (int)($q->fetch_assoc()['c'] ?? 0);
        } else {
            $me = $_SESSION['user_id'] ?? '';
            $st = $conn->prepare("SELECT COUNT(*) AS c FROM appointments WHERE user_id=? AND status IN ('pending','Pending')");
            $st->bind_param('s', $me);
            $st->execute(); $pendingCount = (int)($st->get_result()->fetch_assoc()['c'] ?? 0);
        }
    }
} catch (Throwable $e) { /* ignore header count errors */ }
?>
<!-- Sidebar Navigation for Guidance Admin -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    body {
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }
    .sidebar {
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, #003366 80%, #005fa3 100%);
        color: white;
        position: fixed;
        padding-top: 24px;
        transition: all 0.3s;
        z-index: 100;
        box-shadow: 0 0 24px rgba(0,44,77,.08);
    }
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 19px;
        background: #002855;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: #FFD700;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .sidebar-header i {
        margin-right: 12px;
        font-size: 1.6rem;
        color: #FFD700;
    }
    .sidebar-menu {
        margin-top: 28px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .menu-item {
        text-decoration: none;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 13px 20px;
        font-size: 1.09rem;
        margin: 4px 12px;
        background: #004080;
        transition: all 0.2s;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0,44,77,.06);
        position: relative;
    }
    .menu-item:hover,
    .menu-item.active {
        background: #FFD700;
        color: #003366;
        font-weight: 600;
        transform: scale(1.04);
        box-shadow: 0 4px 18px rgba(0,44,77,.07);
    }
    .menu-item i {
        font-size: 1.2rem;
        color: inherit;
    }
    .notif-badge {
        display:inline-block;
        min-width: 22px;
        padding: 2px 6px;
        margin-left: auto;
        background: #FFD700;
        color: #003366;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        text-align: center;
    }
    .logout-btn {
        text-decoration: none;
        color: white;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px;
        font-size: 1.1rem;
        background: linear-gradient(90deg, #d9534f 60%, #c9302c 100%);
        margin: 38px 14px 12px 14px;
        border-radius: 8px;
        text-align: left;
        transition: all 0.2s;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(220,53,69,.10);
    }
    .logout-btn:hover {
        background: linear-gradient(90deg, #c9302c 60%, #d9534f 100%);
        transform: scale(1.05);
        color: #FFD700;
    }
    @media (max-width: 850px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            border-radius: 0 0 15px 15px;
            box-shadow: none;
        }
        .sidebar-header {
            justify-content: flex-start;
            padding-left: 18px;
        }
        .sidebar-menu {
            flex-direction: row;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .menu-item {
            width: auto;
            min-width: 120px;
            margin: 6px 6px;
        }
        .logout-btn {
            width: 100%;
            text-align: left;
            margin-left: 0;
            margin-right: 0;
        }
    }
</style>
<div class="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-shield-lock"></i>
        <span>Admin Panel</span>
    </div>
    <div class="sidebar-menu">
        <a href="guidance_admin_dashboard.php" class="menu-item" id="dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="guidance_list_admin.php" class="menu-item" id="guidance-requests">
            <i class="bi bi-journal-text"></i> Guidance Requests
            <?php if ($pendingCount > 0): ?>
            <span class="notif-badge" title="Pending requests"><?= htmlspecialchars((string)$pendingCount) ?></span>
            <?php endif; ?>
        </a>
        <a href="guidance_calendar_admin.php" class="menu-item" id="guidance-calendar">
            <i class="bi bi-calendar3"></i> Calendar
        </a>
        <a href="generate_reports.php" class="menu-item" id="generate-reports">
            <i class="bi bi-graph-up"></i> Generate Reports
        </a>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Guidance Admin'): ?>
        <a href="guidance_blackouts_admin.php" class="menu-item" id="blackouts">
            <i class="bi bi-cloud-slash"></i> Blackout Dates
        </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Guidance Admin'): ?>
        <a href="smtp_test.php" class="menu-item" id="smtp-test">
            <i class="bi bi-envelope"></i> SMTP Test
        </a>
        <?php endif; ?>
    </div>
    <a href="login.php" class="logout-btn">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>
<script>
    // Highlight the active menu item
    document.addEventListener('DOMContentLoaded', function() {
        const currentLocation = window.location.pathname.split('/').pop();
        document.querySelectorAll('.menu-item').forEach(item => {
            if (item.getAttribute('href') === currentLocation) {
                item.classList.add('active');
            }
        });
    });
</script>