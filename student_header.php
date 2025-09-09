<?php if (defined('STUDENT_HEADER_INCLUDED')) { return; } define('STUDENT_HEADER_INCLUDED', true); ?>
<?php
// Get avatar for the logged-in user, compatible with either avatar_path or profile_picture
$avatarUrl = null;
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    require_once 'config.php';
    $headerUserId = $_SESSION['user_id'];

    // Dynamically check which avatar column exists
    $avatarColumn = null;
    $colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar_path'");
    if ($colCheck && $colCheck->num_rows > 0) $avatarColumn = 'avatar_path';
    if (!$avatarColumn) {
        $colCheck2 = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
        if ($colCheck2 && $colCheck2->num_rows > 0) $avatarColumn = 'profile_picture';
    }

    $selectCols = "first_name, last_name";
    if ($avatarColumn) $selectCols .= ", $avatarColumn";
    $q = $conn->prepare("SELECT $selectCols FROM users WHERE user_id=? LIMIT 1");
    $q->bind_param("s", $headerUserId);
    $q->execute();
    $res = $q->get_result();
    $headerUser = $res->fetch_assoc();
    $q->close();
    if ($headerUser) {
        $avatarPath = $avatarColumn ? ($headerUser[$avatarColumn] ?? null) : null;
        if ($avatarPath && file_exists($avatarPath)) {
            $avatarUrl = htmlspecialchars($avatarPath);
        } else {
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($headerUser['first_name'] . " " . $headerUser['last_name']) . "&background=0b3c5d&color=fff&rounded=true&size=38";
        }
    }
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/student_theme.css?ver=10">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<nav class="navbar" aria-label="Main">
  <div class="logo" style="user-select:none;display:flex;align-items:center;gap:14px;">
    <span style="letter-spacing:1.5px;font-size:1.22em;">NEUST</span>
    <span style="color:var(--neust-gold);font-weight:900;margin-left:1px;font-size:1.22em;">Gabaldon</span>
    <!-- Avatar beside logo/title REMOVED as requested -->
  </div>
  <div class="nav-container">
    <ul class="nav-links" role="menubar">
      <li role="none"><a role="menuitem" href="student_dashboard.php" tabindex="0"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li role="none"><a role="menuitem" href="student_announcement.php" tabindex="0"><i class="fas fa-bullhorn"></i> Announcements</a></li>
      <li class="dropdown" role="none">
        <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" role="menuitem">
          <i class="fas fa-list"></i> Services
          <span class="caret"><i class="fas fa-chevron-down"></i></span>
        </a>
        <div class="dropdown-content" role="menu">
          <div class="sub-dropdown" role="none">
            <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" role="menuitem">🏠 Dormitory <span class="caret"><i class="fas fa-chevron-right"></i></span></a>
            <div class="sub-dropdown-content" role="menu">
              <a href="rooms.php" tabindex="0" role="menuitem">🏠 Apply</a>
              <a href="check_applications_status.php" tabindex="0" role="menuitem">✅ Check Status</a>
              <a href="student_payments.php" tabindex="0" role="menuitem">💳 Payments</a>
              <a href="dormitory_rules.php" tabindex="0" role="menuitem">📜 Rules</a>
            </div>
          </div>
          <div class="sub-dropdown" role="none">
            <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" role="menuitem">🎓 Scholarship <span class="caret"><i class="fas fa-chevron-right"></i></span></a>
            <div class="sub-dropdown-content" role="menu">
              <a href="scholarships.php" tabindex="0" role="menuitem">📝 Apply</a>
              <a href="track_applications.php" tabindex="0" role="menuitem">📊 Status</a>
              <a href="scholarship_resources.php" tabindex="0" role="menuitem">📚 Resources</a>
            </div>
          </div>
          <div class="sub-dropdown" role="none">
            <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" role="menuitem">🗣️ Guidance <span class="caret"><i class="fas fa-chevron-right"></i></span></a>
            <div class="sub-dropdown-content" role="menu">
              <a href="guidance_request.php" tabindex="0" role="menuitem">📅 Book Appointment</a>
              <a href="student_status_appointments.php" tabindex="0" role="menuitem">📋 Appointment Status</a>
              <a href="guidance_counseling.php" tabindex="0" role="menuitem">🗣️ Counseling</a>
              <a href="guidance_resources.php" tabindex="0" role="menuitem">📖 Resources</a>
            </div>
          </div>
          <div class="sub-dropdown" role="none">
            <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" role="menuitem">⚖️ Grievance <span class="caret"><i class="fas fa-chevron-right"></i></span></a>
            <div class="sub-dropdown-content" role="menu">
              <a href="grievance_filing.php" tabindex="0" role="menuitem">📢 File Complaint</a>
              <a href="submit_grievance.php" tabindex="0" role="menuitem">📢 File Complaint</a>
              <a href="grievance_list.php" tabindex="0" role="menuitem">📄 My Complaints</a>
              <a href="grievance_appointment.php" tabindex="0" role="menuitem">📅 Set Appointment</a>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div class="user-profile">
      <button class="theme-toggle" id="themeToggle" title="Toggle theme" type="button" aria-label="Toggle theme">
        <i class="fas fa-moon"></i>
      </button>
      <div class="user-dropdown">
        <div class="user-icon" onclick="toggleUserDropdown()" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="User menu">
          <?php if ($avatarUrl): ?>
            <img src="<?= $avatarUrl ?>" alt="User Avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--neust-gold);background:#fff;">
          <?php else: ?>
            <i class="fas fa-user"></i>
          <?php endif; ?>
        </div>
        <div class="user-dropdown-content" role="menu">
          <a href="student_profile.php" tabindex="0" role="menuitem"><i class="fas fa-user-circle"></i> My Profile</a>
          <a href="student_dashboard.php" tabindex="0" role="menuitem"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="student_announcement.php" tabindex="0" role="menuitem"><i class="fas fa-bell"></i> Notifications</a>
          <a href="landing_page.php" tabindex="0" role="menuitem"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      </div>
    </div>
  </div>
</nav>
<script>
// THEME: animated, accessible, robust
(function(){
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark' || (!savedTheme && prefersDark)) document.body.classList.add('theme-dark');
  const themeToggleBtn = document.getElementById('themeToggle');
  function setIcon() {
    const isDark = document.body.classList.contains('theme-dark');
    if (themeToggleBtn) {
      themeToggleBtn.innerHTML = isDark
        ? '<i class="fas fa-sun fa-spin" style="color:#f6ae2d;"></i>'
        : '<i class="fas fa-moon"></i>';
    }
  }
  if (themeToggleBtn) {
    setIcon();
    themeToggleBtn.addEventListener('click', function(){
      document.body.classList.toggle('theme-dark');
      localStorage.setItem('theme', document.body.classList.contains('theme-dark') ? 'dark' : 'light');
      setIcon();
    });
    themeToggleBtn.addEventListener('keydown', e => {
      if (e.key === "Enter" || e.key === " ") {
        themeToggleBtn.click();
      }
    });
  }
})();
// NAVIGATION: dropdowns, keyboard, accessible, sub-dropdown overflow
(function(){
  const closeAllMenus = () => {
    document.querySelectorAll('.dropdown.active, .sub-dropdown.active, .user-dropdown.active')
      .forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.dropdown > a, .sub-dropdown > a').forEach(a => a.setAttribute('aria-expanded', 'false'));
  };
  document.querySelectorAll('.dropdown > a').forEach(link => {
    link.addEventListener('click', function(e){
      e.preventDefault();
      const parent = this.parentElement;
      const expanded = parent.classList.contains('active');
      closeAllMenus();
      if (!expanded) {
        parent.classList.add('active');
        this.setAttribute('aria-expanded', 'true');
      }
    });
    link.addEventListener('keydown', function(e){
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        this.click();
      }
    });
  });
  document.querySelectorAll('.sub-dropdown > a').forEach(link => {
    link.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      const parent = this.parentElement;
      const expanded = parent.classList.contains('active');
      parent.parentElement.querySelectorAll('.sub-dropdown.active').forEach(s => { if (s !== parent) s.classList.remove('active'); });
      parent.classList.toggle('active');
      this.setAttribute('aria-expanded', String(!expanded));
      setTimeout(() => {
        const submenu = parent.querySelector('.sub-dropdown-content');
        if (submenu) {
          submenu.classList.remove('align-left');
          const rect = submenu.getBoundingClientRect();
          if (rect.right > window.innerWidth - 10) {
            submenu.classList.add('align-left');
          }
        }
      }, 10);
    });
    link.addEventListener('keydown', function(e){
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        this.click();
      }
    });
  });
  window.addEventListener('resize', function() {
    document.querySelectorAll('.sub-dropdown.active .sub-dropdown-content').forEach(submenu => {
      submenu.classList.remove('align-left');
      const rect = submenu.getBoundingClientRect();
      if (rect.right > window.innerWidth - 10) {
        submenu.classList.add('align-left');
      }
    });
  });
  document.addEventListener('click', function(e){
    if (!e.target.closest('.navbar')) { closeAllMenus(); }
  });
  const svc = document.querySelectorAll('.nav-links > li.dropdown');
  if (svc.length > 1) {
    for (let i = 1; i < svc.length; i++) svc[i].parentElement.removeChild(svc[i]);
  }
})();
// USER MENU: click & keyboard accessible
function toggleUserDropdown(){
  const dd = document.querySelector('.user-dropdown');
  if (dd) {
    dd.classList.toggle('active');
    const menu = dd.querySelector('.user-dropdown-content');
    if (menu) menu.setAttribute('aria-expanded', dd.classList.contains('active') ? 'true' : 'false');
  }
}
document.addEventListener('keydown', function(e){
  if ((e.key === "Enter" || e.key === " ") && document.activeElement.classList.contains('user-icon')) {
    toggleUserDropdown();
  }
});
</script>