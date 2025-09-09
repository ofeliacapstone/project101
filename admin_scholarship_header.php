<?php
if (defined('ADMIN_SCHOLARSHIP_HEADER_INCLUDED')) { return; }
define('ADMIN_SCHOLARSHIP_HEADER_INCLUDED', true);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar scholarship-sidebar" role="navigation" aria-label="Scholarship Admin">
	<div class="sidebar-header">
		<i class="fa fa-graduation-cap" aria-hidden="true"></i>
		<div class="brand-text">
			<div class="brand-title">Scholarship Admin</div>
			<div class="brand-subtitle">NEUST Gabaldon</div>
		</div>
	</div>
	<nav class="sidebar-menu" aria-label="Primary">
		<a href="scholarship_admin_dashboard.php" class="menu-item" id="dashboard">
			<i class="fas fa-tachometer-alt" aria-hidden="true"></i>
			<span>Dashboard</span>
		</a>
		<a href="admin_manage_scholarships.php" class="menu-item" id="manage-scholars">
			<i class="fas fa-graduation-cap" aria-hidden="true"></i>
			<span>Manage Scholarships</span>
		</a>
		<a href="manage_applications.php" class="menu-item" id="applications">
			<i class="fas fa-file-alt" aria-hidden="true"></i>
			<span>Applications</span>
		</a>
		<a href="approved_scholars.php" class="menu-item" id="approved-scholars">
			<i class="fas fa-check-circle" aria-hidden="true"></i>
			<span>Approved Scholars</span>
		</a>
	</nav>
	<a href="login.php" class="logout-btn">
		<i class="fas fa-sign-out-alt" aria-hidden="true"></i>
		<span>Logout</span>
	</a>
</div>
<style>
	:root {
		--sch-blue: #003366;
		--sch-blue-dark: #002855;
		--sch-blue-grad: #00509E;
		--sch-blue-mid: #004080;
		--sch-gold: #FFD700;
		--sch-danger: #d9534f;
		--sch-danger-dark: #c9302c;
		--sch-sidebar-w: 260px;
	}
	.scholarship-sidebar {
		width: var(--sch-sidebar-w);
		height: 100vh;
		background: linear-gradient(180deg, var(--sch-blue) 78%, var(--sch-blue-grad) 100%);
		color: #fff;
		position: fixed;
		padding-top: 20px;
		transition: transform 0.25s ease;
		z-index: 1020;
		box-shadow: 0 0 24px rgba(0,44,77,.08);
		display: flex;
		flex-direction: column;
		overflow-y: auto;
	}
	.scholarship-sidebar .sidebar-header {
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 12px;
		padding: 20px;
		background-color: var(--sch-blue-dark);
		font-weight: 700;
		text-transform: uppercase;
		color: var(--sch-gold);
		border-radius: 12px;
		box-shadow: 0 4px 12px rgba(0,0,0,0.08);
		margin: 0 12px;
	}
	.scholarship-sidebar .sidebar-header i {
		margin-right: 2px;
		color: var(--sch-gold);
		font-size: 1.5rem;
	}
	.scholarship-sidebar .brand-text { line-height: 1.1; }
	.scholarship-sidebar .brand-title { font-size: 1.05rem; letter-spacing: .4px; }
	.scholarship-sidebar .brand-subtitle { font-size: .72rem; color: rgba(255,255,255,.8); text-transform: none; font-weight: 600; }
	.scholarship-sidebar .sidebar-menu {
		margin-top: 20px;
		display: flex;
		flex-direction: column;
		gap: 10px;
		flex: 1 1 auto;
	}
	.scholarship-sidebar .menu-item {
		text-decoration: none;
		color: #fff;
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 14px 18px;
		font-size: 1.03rem;
		margin: 0 12px;
		background: linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
		border: 1px solid rgba(255,255,255,.10);
		backdrop-filter: blur(6px);
		transition: transform 0.15s ease, background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
		border-radius: 12px;
		font-weight: 500;
		box-shadow: 0 2px 10px rgba(0,44,77,.08);
		position: relative;
		overflow: hidden;
	}
	.scholarship-sidebar .menu-item::before {
		content: "";
		position: absolute;
		left: 0; top: 0; bottom: 0;
		width: 3px;
		background: transparent;
		transition: background 0.15s ease, width 0.15s ease;
		border-radius: 2px;
	}
	.scholarship-sidebar .menu-item:hover::before,
	.scholarship-sidebar .menu-item.active::before { background: var(--sch-gold); }
	.scholarship-sidebar .menu-item:hover {
		transform: translateY(-1px);
		border-color: rgba(255,255,255,.18);
		box-shadow: 0 6px 18px rgba(0,44,77,.14);
	}
	.scholarship-sidebar .menu-item.active {
		background-color: var(--sch-gold);
		color: var(--sch-blue);
		box-shadow: 0 6px 18px rgba(0,44,77,.14);
		border-color: rgba(255,215,0,.85);
	}
	.scholarship-sidebar .menu-item i { color: inherit; font-size: 1.1rem; }
	.scholarship-sidebar .menu-item:focus-visible {
		outline: 2px solid var(--sch-gold);
		outline-offset: 2px;
	}
	.scholarship-sidebar .logout-btn {
		text-decoration: none;
		color: #fff;
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 13px 16px;
		font-size: 1.02rem;
		background: linear-gradient(90deg, var(--sch-danger) 60%, var(--sch-danger-dark) 100%);
		margin: 18px 12px 16px 12px;
		border-radius: 12px;
		text-align: left;
		transition: transform 0.15s ease, background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
		font-weight: 650;
		box-shadow: 0 3px 10px rgba(220,53,69,.16);
		margin-top: auto;
	}
	.scholarship-sidebar .logout-btn:hover {
		background: linear-gradient(90deg, var(--sch-danger-dark) 60%, var(--sch-danger) 100%);
		transform: translateY(-1px);
		color: var(--sch-gold);
	}
	@media (max-width: 850px) {
		.scholarship-sidebar {
			width: 100%;
			height: auto;
			position: relative;
			border-radius: 0 0 15px 15px;
			box-shadow: none;
		}
		.scholarship-sidebar .sidebar-header { justify-content: flex-start; padding-left: 18px; }
		.scholarship-sidebar .sidebar-menu { flex-direction: row; flex-wrap: wrap; margin-top: 12px; }
		.scholarship-sidebar .menu-item { width: auto; min-width: 140px; margin: 6px 6px; }
	}
</style>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const currentLocation = window.location.pathname.split('/').pop();
		const menuItems = document.querySelectorAll('.scholarship-sidebar .menu-item');
		menuItems.forEach(item => {
			const href = item.getAttribute('href');
			if (href === currentLocation) {
				item.classList.add('active');
				item.setAttribute('aria-current', 'page');
			}
		});
	});
</script>