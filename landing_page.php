<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db"; 
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM announcements ORDER BY date_posted DESC";
$result = $conn->query($sql);
require_once __DIR__ . '/csrf.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEUST Gabaldon Student Services</title>
      <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
      <!-- Landing-only CSS -->
    <link rel="stylesheet" href="assets/landing/landing.css">
    <!-- Auth Split UI CSS -->
    <link rel="stylesheet" href="assets/landing/auth.css">
    <style>
         /* Slideshow (namespaced to landing page) */
        .landing-page .slideshow-container { max-width: 85%; margin: 20px auto; position: relative; }
        .landing-page .slide { position: relative; overflow: hidden; border-radius: 10px; transition: transform 0.3s ease-in-out; }
        .landing-page .slide:hover { transform: scale(1.02); }
        .landing-page .slide img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 10px; }
        .landing-page .caption { position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); background: rgba(0, 0, 0, 0.7); color: white; padding: 12px; width: 80%; text-align: center; font-size: 18px; border-radius: 5px; }
        .landing-page .slick-prev, .landing-page .slick-next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.7); color: black; border: none; font-size: 22px; padding: 10px; cursor: pointer; z-index: 10; border-radius: 50%; }
        .landing-page .slick-prev { left: -50px; }
        .landing-page .slick-next { right: -50px; }
        .landing-page .slick-prev:hover, .landing-page .slick-next:hover { background: #fff; color: black; }
        .landing-page .slick-dots { bottom: 15px; }
        .landing-page .slick-dots li button:before { color: #fff; font-size: 15px; }
        .landing-page .slick-dots li.slick-active button:before { color: #ffd700; }
        @media (max-width: 640px){
            .landing-page .slick-prev { left: 12px; }
            .landing-page .slick-next { right: 12px; }
        }
    </style>
</head>
<body class="landing-page">
    <nav class="nav">
        <div class="container nav-inner">
            <a class="brand" href="#" aria-label="NEUST Home">
                <img src="assets/logo.png" alt="NEUST Gabaldon Logo" loading="lazy">
                <span>NEUST Student Services</span>
            </a>
            <div class="nav-links" role="navigation" aria-label="Primary">
                <a href="#features">Features</a>
                <a href="#announcements">Announcements</a>
                <button id="modeToggle" class="mode-toggle" aria-label="Toggle dark mode"><i class="fa-regular fa-moon"></i></button>
                <a href="#" id="openLogin" class="btn btn-ghost" aria-haspopup="dialog">Login</a>
                <a href="#" id="openRegister" class="btn btn-primary" aria-haspopup="dialog">Register</a>
            </div>
        </div>
          </nav>

            <section class="hero">
        <div class="container hero-grid">
            <div>
                <h1 class="headline reveal">Student Services, Simplified</h1>
                <p class="subhead reveal" style="transition-delay:.08s">Access scholarships, dormitory rooms, guidance, and more — fast, modern, and secure.</p>
                <div class="cta-row reveal" style="transition-delay:.16s">
                    <button id="ctaRegister" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Create account</button>
                    <button id="ctaLogin" class="btn btn-ghost"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
                </div>
            </div>
              <div class="hero-visual reveal" style="transition-delay:.24s" aria-hidden="true">
                <div class="blob b1"></div>
                <div class="blob b2"></div>
                <div class="blob b3"></div>
                <img src="assets/logo.png" alt="NEUST" class="hero-logo" loading="lazy">
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section" id="features">
        <div class="container">
            <h3 class="section-title">Why NEUST Student Services</h3>
            <p class="section-sub">Modern, reliable, and designed for you.</p>
            <div class="grid grid-4">
                <div class="card reveal"><div class="card-icon ic-blue"><i class="fa-solid fa-bolt"></i></div><h4>Fast & Seamless</h4><p>Optimized flows to apply, track, and manage in minutes.</p></div>
                <div class="card reveal"><div class="card-icon ic-cyan"><i class="fa-solid fa-shield-halved"></i></div><h4>Secure</h4><p>Role-based access, CSRF protection, and audit logs.</p></div>
                <div class="card reveal"><div class="card-icon ic-gold"><i class="fa-solid fa-sparkles"></i></div><h4>Modern UI</h4><p>Glassmorphism, animations, and accessible interactions.</p></div>
                <div class="card reveal"><div class="card-icon ic-green"><i class="fa-solid fa-mobile-screen"></i></div><h4>Responsive</h4><p>Works great from mobile to desktop without compromise.</p></div>
            </div>
        </div>
    </section>

    <!-- Announcements slideshow -->
    <section class="section" id="announcements">
        <div class="container">
            <h3 class="section-title">Announcements</h3>
            <p class="section-sub">Latest updates and news from NEUST Gabaldon.</p>
            <div class="slideshow-container reveal" aria-label="Announcements slideshow">
                <?php mysqli_data_seek($result, 0); if ($result && $result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                    <div class="slide">
                        <a href="announcement_details.php?id=<?= htmlspecialchars($row['id']) ?>">
                            <img src="uploads/announcements/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/logo.png';">
                            <div class="caption"><?= htmlspecialchars($row['title']) ?></div>
                        </a>
                    </div>
                <?php endwhile; else: ?>
                    <div class="slide">
                        <div class="card" style="padding:20px; text-align:center;">
                            <img src="assets/logo.png" alt="NEUST" loading="lazy" style="width:90px; margin:10px auto 6px; opacity:.9;">
                            <div style="font-weight:700;">No announcements yet</div>
                            <div class="section-sub">Please check back later.</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div style="margin-top:12px"><a href="student_announcement.php" class="btn btn-primary"><i class="fa-solid fa-bullhorn"></i> View Announcements</a></div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section" id="how">
        <div class="container">
            <h3 class="section-title">How it works</h3>
            <div class="grid grid-3">
                <div class="card reveal"><div class="card-icon ic-gold"><i class="fa-solid fa-user-plus"></i></div><h4>Create your account</h4><p>Register in seconds with your basic details.</p></div>
                <div class="card reveal"><div class="card-icon ic-blue"><i class="fa-solid fa-file-signature"></i></div><h4>Apply or Request</h4><p>Guidance, dorm rooms, scholarships, and more.</p></div>
                <div class="card reveal"><div class="card-icon ic-cyan"><i class="fa-solid fa-check-circle"></i></div><h4>Get updates</h4><p>Track status, receive emails, and manage actions.</p></div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section" id="testimonials">
        <div class="container">
            <h3 class="section-title">Trusted by students</h3>
            <div class="grid grid-3">
                <div class="card reveal"><p class="quote">“Super dali gamitin, ang bilis mag-apply at mag-track.”</p><div style="display:flex; align-items:center; gap:10px; margin-top:10px"><div class="avatar"></div><div><strong>J. Santos</strong><div class="muted">BSIT</div></div></div></div>
                <div class="card reveal"><p class="quote">“Modern UI, smooth and walang hassle sa submissions.”</p><div style="display:flex; align-items:center; gap:10px; margin-top:10px"><div class="avatar"></div><div><strong>A. Cruz</strong><div class="muted">Education</div></div></div></div>
                <div class="card reveal"><p class="quote">“Finally, one place for dorm, guidance, and scholarships.”</p><div style="display:flex; align-items:center; gap:10px; margin-top:10px"><div class="avatar"></div><div><strong>K. Dela Cruz</strong><div class="muted">Engineering</div></div></div></div>
            </div>
        </div>
    </section>

    <!-- CTA strip -->
    <section class="section">
        <div class="container">
            <div class="cta-strip reveal">
                <div>
                    <h4 style="margin:0 0 6px; font-weight:800; color:#fff;">Ready to get started?</h4>
                    <div style="opacity:.9">Create your account and apply today.</div>
                </div>
                <div><a href="#" id="openRegisterBottom" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Register Now</a></div>
            </div>
        </div>
    </section>
      <div class="about reveal" id="about">
        <h2>About Us</h2>
        <p>
            NEUST Gabaldon Student Services Management System is designed to optimize and enhance the management of various student services. 
            Our goal is to provide an efficient, user-friendly platform for students and faculty to access essential services such as announcements, 
            scholarships, grievances, and dormitory management.
        </p>
    </div>

  <div class="services reveal" id="services">
        <h2>Our Services</h2>
        <div class="service">
            <h3>Announcements</h3>
            <p>Stay updated with the latest news and announcements from NEUST Gabaldon. Our platform ensures you never miss important updates.</p>
        </div>
        <div class="service">
            <h3>Scholarships</h3>
            <p>Apply for various scholarships offered by NEUST Gabaldon. Our platform provides an optimized application process to help you secure financial support.</p>
        </div>
        <div class="service">
            <h3>Grievances</h3>
            <p>Have any concerns or issues? Use our grievance service to report and resolve your problems efficiently and effectively.</p>
        </div>
        <div class="service">
            <h3>Dormitory Services</h3>
            <p>Manage your dormitory applications and stay updated with dormitory services offered by NEUST Gabaldon.</p>
        </div>
    </div>
    <footer class="footer">
        <div class="container footer-grid">
            <div>
                <strong>NEUST Student Services</strong>
                <p class="section-sub">All-in-one portal for students.</p>
                <div class="socials" aria-label="Social links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            <div>
                <strong>Explore</strong>
                <div><a href="student_announcement.php">Announcements</a></div>
                <div><a href="#features">Features</a></div>
            </div>
            <div>
                <strong>Support</strong>
                <div><a href="#" id="openLoginFooter">Login</a></div>
                <div><a href="#" id="openRegisterFooter">Register</a></div>
                <div><a href="mailto:support@example.com">Contact</a></div>
            </div>
        </div>
        <div class="container" style="margin-top:12px; color:var(--muted);">&copy; 2025 NEUST Gabaldon. All Rights Reserved.</div>
    </footer>

    <!-- Overlay Modals -->
    <style>
        .overlay-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: none; align-items: center; justify-content: center; z-index: 1050; opacity: 0; transition: opacity .25s ease; }
        .overlay-backdrop.open { opacity: 1; }
        .overlay-modal { width: 90%; max-width: 980px; height: 85vh; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.35); overflow: hidden; position: relative; transform: translateY(16px) scale(.98); opacity: 0; transition: transform .3s ease, opacity .3s ease; }
        .overlay-backdrop.open .overlay-modal { transform: translateY(0) scale(1); opacity: 1; }
        .overlay-header { position: absolute; top: 0; left: 0; right: 0; height: 50px; background: rgb(2, 31, 61); color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; }
        .overlay-title { font-weight: 700; }
        .overlay-close { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; }
        .overlay-body { position: absolute; top: 50px; left: 0; right: 0; bottom: 0; }
        .overlay-body iframe { width: 100%; height: 100%; border: 0; }
        @media (max-width: 768px){ .overlay-modal{ width: 96%; height: 90vh; } }

        /* Background blur when modal open */
        body.modal-blur .navbar,
        body.modal-blur .hero,
        body.modal-blur .slideshow-container,
        body.modal-blur .about,
        body.modal-blur .services,
        body.modal-blur .footer { filter: blur(6px) brightness(.9); transition: filter .25s ease; pointer-events: none; }
    </style>
    <!-- Auth Split Overlay -->
    <div class="auth-overlay" id="authOverlay" aria-hidden="true">
        <div class="auth-bg" aria-hidden="true">
            <div class="auth-blob b1"></div>
            <div class="auth-blob b2"></div>
        </div>
        <div class="auth-card" id="authCard" role="dialog" aria-modal="true" aria-labelledby="authTitle">
            <button class="auth-close auth-switch" id="authClose" aria-label="Close">×</button>
            <!-- Left: Sign In -->
            <div class="auth-side auth-left">
                <div id="authPaneLogin" class="auth-pane visible">
                    <div class="auth-brand">
                        <img src="assets/logo.png" alt="NEUST" class="auth-logo small" loading="lazy">
                        <strong>NEUST</strong>
                    </div>
                    <h3 class="auth-title" id="authTitle">Welcome Back!</h3>
                    <div class="auth-sub">Sign in to continue</div>
                    <form class="auth-form" method="POST" action="login.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
                        <input class="auth-input" type="text" name="user_id" placeholder="User ID" required aria-label="User ID">
                        <div class="auth-input-wrap">
                            <input class="auth-input" type="password" id="loginPwd" name="password" placeholder="Password" required aria-label="Password">
                            <button type="button" class="auth-eye fa-solid fa-eye" data-eye="loginPwd" aria-label="Show password"></button>
                        </div>
                        <button class="auth-btn" type="submit">Sign In</button>
                        <div class="auth-socials" aria-label="Social login">
                            <a href="#" aria-label="Sign in with Google"><i class="fa-brands fa-google"></i></a>
                            <a href="#" aria-label="Sign in with Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" aria-label="Sign in with Apple"><i class="fa-brands fa-apple"></i></a>
                        </div>
                        <div style="margin-top:8px">No account? <a href="#" id="toRegister" style="text-decoration:underline; color:#fff">Create one</a></div>
                    </form>
                </div>
            </div>
            <!-- Right: Sign Up -->
            <div class="auth-side auth-right">
                <div id="authPaneRegister" class="auth-pane hidden">
                    <img src="assets/logo.png" alt="NEUST Logo" class="auth-logo" loading="lazy">
                    <h3 class="auth-title">Create Account</h3>
                    <div class="auth-sub">Join and get started</div>
                    <form class="auth-form" method="POST" action="register.php" id="registerLite">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
                        <input class="auth-input" type="text" name="first_name" placeholder="First Name" required aria-label="First Name">
                        <input class="auth-input" type="email" name="email" placeholder="Email" required aria-label="Email">
                        <div class="auth-input-wrap">
                            <input class="auth-input" type="password" id="regPwd" name="password" placeholder="Password" required aria-label="Password">
                            <button type="button" class="auth-eye fa-solid fa-eye" data-eye="regPwd" aria-label="Show password"></button>
                        </div>
                        <button class="auth-btn auth-btn-alt" type="submit">Sign Up</button>
                        <div class="auth-socials" aria-label="Social register">
                            <a href="#" aria-label="Sign up with Google"><i class="fa-brands fa-google"></i></a>
                            <a href="#" aria-label="Sign up with Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" aria-label="Sign up with Apple"><i class="fa-brands fa-apple"></i></a>
                        </div>
                        <div style="margin-top:8px">Have an account? <a href="#" id="toLogin" style="text-decoration:underline;">Sign in</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery & Slick Carousel Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="assets/landing/landing.js"></script>
    <script src="assets/landing/auth.js"></script>
    <script>
           // Initialize announcements slideshow (same as student)
        $(document).ready(function(){
            $('.slideshow-container').slick({
                 dots: true,
                infinite: true,
                  speed: 600,
                fade: false,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                prevArrow: '<button class="slick-prev">&#10094;</button>',
                nextArrow: '<button class="slick-next">&#10095;</button>'
            });
        });
    </script>
</body>
</html>


