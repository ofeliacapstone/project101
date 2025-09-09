<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id); $stmt->execute();
$result = $stmt->get_result(); $user = $result->fetch_assoc();
$stmt->close();
if (!$user) { session_destroy(); header("Location: login.php?error=user_not_found"); exit(); }
function safe($x) { return htmlspecialchars($x ?? "", ENT_QUOTES, 'UTF-8'); }
function safe_num($x, $dec=0) { return is_numeric($x) ? number_format($x, $dec) : "N/A"; }
function safe_date($dt, $fmt='M d, Y h:i A') {
    if (!$dt || in_array(substr($dt,0,10), ['0000-00-00', '1970-01-01'])) return "N/A";
    return date($fmt, strtotime($dt));
}
$profilePic = (!empty($user['profile_picture']) && file_exists($user['profile_picture']))
    ? safe($user['profile_picture'])
    : "https://ui-avatars.com/api/?name=".urlencode(trim($user['first_name'].' '.$user['last_name']))."&background=1E3A8A&color=fff&rounded=true&size=140";
$gpa = safe_num($user['gpa'], 2);
$family_income = is_numeric($user['family_income']) ? '₱'.number_format($user['family_income'],0) : "N/A";
$last_login = safe_date($user['last_login']);
$date_registered = safe_date($user['date_registered'], "M d, Y");
$isActive = (strtolower($user['status']??'') === 'active');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile | NEUST Gabaldon</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
<style>
body {
    background: linear-gradient(135deg, #1E3A8A 0%, #FFD700 100%);
    min-height: 100vh;
    font-family: 'Inter', 'Poppins', Arial, sans-serif;
}
.glass-card {
    background: rgba(255,255,255,0.9);
    border-radius:1.7rem;
    box-shadow:0 12px 32px #1E3A8A17;
    padding:2.2rem 1.6rem 2rem 1.6rem;
    margin-bottom:1.3rem;
    backdrop-filter: blur(4.5px);
    transition: box-shadow 0.25s, transform 0.18s;
}
.glass-card:hover {
    box-shadow:0 16px 48px #FFD70055;
    transform: translateY(-4px) scale(1.015);
}
.profile-avatar-box { position: relative; display: inline-block; }
.profile-avatar {
    width: 128px; height: 128px; border-radius: 50%; object-fit: cover;
    border: 5px solid #FFD700; background: #f7fafc;
    box-shadow: 0 3px 18px #FFD70038;
    transition: box-shadow 0.24s;
    animation: fadeInAvatar 0.6s cubic-bezier(0.23,1,0.32,1);
}
@keyframes fadeInAvatar {
    0% { opacity: 0; transform: scale(0.96);}
    100% { opacity: 1; transform: scale(1);}
}
.profile-avatar:hover { box-shadow: 0 4px 32px #FFD70077; }
.avatar-upload-btn {
    position: absolute; right: 0; bottom: 0;
    background: #1E3A8A; color: #FFD700; border-radius: 50%; border: 2.5px solid #fff;
    width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; cursor: pointer; box-shadow: 0 2px 8px #1E3A8A44;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.avatar-upload-btn:hover { background: #FFD700; color: #1E3A8A; box-shadow: 0 4px 16px #FFD70055;}
.profile-header-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}
.profile-name {
    font-size: 1.3rem;
    font-weight: 900;
    color: #1E3A8A;
    letter-spacing: .01em;
    margin-bottom: 0;
    margin-right: 0.2rem;
}
.status-badge-pro {
    font-size: .88rem;
    font-weight: 600;
    border-radius: 1em;
    padding: 0.13em 0.7em 0.13em 0.6em;
    background: #f4f5fa;
    color: #1E3A8A;
    vertical-align: middle;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 2px 8px #1E3A8A10;
    border: 1px solid #e5e5e5;
    margin-top: 0.05rem;
    margin-bottom: 0.05rem;
    margin-left: 0.1rem;
    gap: 0.35em;
    min-width: 75px;
}
.status-dot {
    display:inline-block;width:9px;height:9px;border-radius:50%;
    background: <?= $isActive ? "#22c55e" : "#9ca3af" ?>;
    border:1.5px solid #fff;
    margin-right: 4px;
}
.profile-meta { color: #555; font-size: 1.06rem; margin-top:3px;}
.user-id-badge {
    font-size: .95rem;
    color: #fff;
    background: #1E3A8A;
    border-radius: 1.2rem;
    padding: .17rem 0.7rem;
    font-weight: 600;
    margin-top: 6px;
    display: inline-block;
    letter-spacing: .01em;
    box-shadow: 0 1.5px 8px #1E3A8A18;
}
.gold-pill, .info-pill {
    display: inline-block; border-radius: 2rem; padding: .22rem .75rem; font-weight: 700;
    font-size: .97rem; background: #FFD700; color: #1E3A8A; margin-right:.5rem; margin-bottom: 3px;
    transition: box-shadow 0.18s;
}
.gold-pill:hover, .family-card:hover { box-shadow: 0 2px 12px #FFD70044; }
.info-pill { background: #1E3A8A; color: #FFD700; margin-right:0; }
.family-card {
    border:2px solid #FFD700; background:#fffbe9; font-size: .96rem; padding: .21rem .8rem;
    display: inline-block; margin-bottom: 4px; transition: box-shadow 0.18s;
}
.meta-mini { font-size:.97rem; color:#57607a; margin-top:4px; }
.accordion .accordion-item {
    border-radius:1.12rem; overflow:hidden; border-width:0;
    box-shadow: 0 2px 14px #FFD70013; margin-bottom: 1.15rem;
    background: rgba(255,255,255,0.92);
    animation: fadeInUp 0.6s cubic-bezier(0.23,1,0.32,1);
}
@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(22px);}
    100% { opacity: 1; transform: translateY(0);}
}
.accordion-button {
    background: #f4f8ff; color: #1E3A8A; font-weight: 800;
    font-size: 1.07rem; letter-spacing: .01em; transition: background 0.22s;
}
.accordion-button:focus, .accordion-button:not(.collapsed) { background: #e0e7ff; color: #1E3A8A; }
.edit-btn {
    transition: background 0.22s, color 0.22s; font-weight: 700; padding: .5rem 1.3rem;
}
.edit-btn:hover { background: #1E3A8A; color: #FFD700; border-color: #FFD700; }
.save-btn, .cancel-btn { transition: background 0.2s, color 0.2s; font-weight: 700; }
.save-btn:hover { background: #22c55e !important; color: #fff !important;}
.cancel-btn:hover { background: #ef4444 !important; color: #fff !important;}
.toast { font-weight: 500; letter-spacing: .01em;}
/* Section divider line */
.section-divider {
    border: none;
    border-top: 1px solid #FFD70044;
    margin: 1.4em 0 1em 0;
}
@media (max-width:900px){ .glass-card {margin-bottom:1.5rem;} }
@media (max-width:600px){
    .glass-card {padding:1.15rem;}
    .profile-avatar {width: 92px; height:92px;}
    .profile-header-box {flex-direction: column;align-items: flex-start;}
}
</style>
</head>
<body>
<?php include 'student_header.php'; ?>
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar/Profile Card -->
        <div class="col-xl-3 col-lg-4">
            <div class="glass-card text-center">
                <div class="profile-avatar-box mb-2">
                    <img src="<?= safe($profilePic) ?>" alt="Profile Picture" class="profile-avatar" id="profilePicPreview">
                    <span class="avatar-upload-btn" title="Change Photo" onclick="document.getElementById('profilePicInput').click()">
                        <i class="fa fa-camera"></i>
                    </span>
                    <form id="picForm" enctype="multipart/form-data" method="POST" action="update_profile.php" class="d-none">
                        <input type="file" name="profile_picture" id="profilePicInput" accept="image/*">
                    </form>
                </div>
                <div class="profile-header-box">
                    <div class="profile-name mb-0"><?= safe($user['first_name'].' '.$user['last_name']) ?></div>
                    <span class="status-badge-pro">
                        <span class="status-dot"></span>
                        <?= $isActive ? "Active" : "Inactive" ?>
                    </span>
                </div>
                <div class="user-id-badge">Student ID: <?= safe($user['user_id']) ?></div>
                <div class="profile-meta mt-2"><?= safe($user['course'] ?? '') ?> <?= ($user['year'] ?? '') ? 'Year '.safe($user['year']) : '' ?> <?= safe($user['section']) ?></div>
                <div class="mt-3 mb-2">
                    <span class="gold-pill"><i class="fa fa-graduation-cap"></i> GPA: <?= $gpa ?></span>
                    <span class="family-card"><i class="fa fa-coins"></i> Family Income: <?= $family_income ?></span>
                </div>
                <div class="meta-mini">
                    <i class="fa fa-clock"></i> Last Login: <?= $last_login ?><br>
                    <i class="fa fa-calendar-plus"></i> Registered: <?= $date_registered ?>
                </div>
            </div>
        </div>
        <!-- Main Profile Panels -->
        <div class="col-xl-9 col-lg-8">
            <div class="glass-card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="fa fa-user-graduate gold-text"></i> Student Profile</h4>
                    <button id="editBtn" class="btn btn-outline-primary edit-btn"><i class="fa fa-pen"></i> Edit</button>
                </div>
                <form id="profileForm" action="update_profile.php" method="POST" class="accordion" autocomplete="off">
                    <!-- Personal Details Panel -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="personal-heading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#personal-panel" aria-expanded="true" aria-controls="personal-panel">
                                <i class="fa fa-user me-2"></i> Personal Details
                            </button>
                        </h2>
                        <div id="personal-panel" class="accordion-collapse collapse show" aria-labelledby="personal-heading" data-bs-parent="#profileForm">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= safe($user['first_name']) ?>" required disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control" value="<?= safe($user['middle_name']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= safe($user['last_name']) ?>" required disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Birthdate</label>
                                        <input type="date" name="birth_date" class="form-control" value="<?= safe($user['birth_date']) ?>" required disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nationality</label>
                                        <input type="text" name="nationality" class="form-control" value="<?= safe($user['nationality']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Religion</label>
                                        <input type="text" name="religion" class="form-control" value="<?= safe($user['religion']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Biological Sex</label>
                                        <select name="biological_sex" class="form-select" required disabled>
                                            <option value="">Select</option>
                                            <option <?= $user['biological_sex']=='Male'?'selected':'' ?>>Male</option>
                                            <option <?= $user['biological_sex']=='Female'?'selected':'' ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <!-- Academic Info Panel -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="academic-heading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#academic-panel" aria-expanded="false" aria-controls="academic-panel">
                                <i class="fa fa-book me-2"></i> Academic Information
                            </button>
                        </h2>
                        <div id="academic-panel" class="accordion-collapse collapse" aria-labelledby="academic-heading" data-bs-parent="#profileForm">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Role</label>
                                        <input type="text" name="role" class="form-control" value="<?= safe($user['role']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Year</label>
                                        <input type="number" name="year" class="form-control" value="<?= safe($user['year']) ?>" min="1" max="6" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Section</label>
                                        <input type="text" name="section" class="form-control" value="<?= safe($user['section']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Course</label>
                                        <input type="text" name="course" class="form-control" value="<?= safe($user['course']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">GPA</label>
                                        <input type="number" step="0.01" name="gpa" class="form-control" value="<?= safe($user['gpa']) ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <!-- Family Background Panel -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="family-heading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#family-panel" aria-expanded="false" aria-controls="family-panel">
                                <i class="fa fa-users me-2"></i> Family Background
                            </button>
                        </h2>
                        <div id="family-panel" class="accordion-collapse collapse" aria-labelledby="family-heading" data-bs-parent="#profileForm">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Mother's Name</label>
                                        <input type="text" name="mother_name" class="form-control" value="<?= safe($user['mother_name']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Mother's Work</label>
                                        <input type="text" name="mother_work" class="form-control" value="<?= safe($user['mother_work']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Mother's Contact</label>
                                        <input type="text" name="mother_contact" class="form-control" value="<?= safe($user['mother_contact']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Father's Name</label>
                                        <input type="text" name="father_name" class="form-control" value="<?= safe($user['father_name']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Father's Work</label>
                                        <input type="text" name="father_work" class="form-control" value="<?= safe($user['father_work']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Father's Contact</label>
                                        <input type="text" name="father_contact" class="form-control" value="<?= safe($user['father_contact']) ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Number of Siblings</label>
                                        <input type="number" name="siblings_count" class="form-control" value="<?= safe($user['siblings_count']) ?>" min="0" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Family Income</label>
                                        <input type="number" step="1000" name="family_income" class="form-control" value="<?= safe($user['family_income']) ?>" disabled style="max-width:120px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <!-- Contact Info Panel -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="contact-heading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contact-panel" aria-expanded="false" aria-controls="contact-panel">
                                <i class="fa fa-phone me-2"></i> Contact Info
                            </button>
                        </h2>
                        <div id="contact-panel" class="accordion-collapse collapse" aria-labelledby="contact-heading" data-bs-parent="#profileForm">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= safe($user['email']) ?>" required disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" value="<?= safe($user['phone']) ?>" maxlength="11" pattern="[0-9]{11}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Current Address</label>
                                        <input type="text" name="current_address" class="form-control" value="<?= safe($user['current_address']) ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Permanent Address</label>
                                        <input type="text" name="permanent_address" class="form-control" value="<?= safe($user['permanent_address']) ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <!-- System Logs Panel (READ ONLY, not editable, not submitted!) -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="system-heading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#system-panel" aria-expanded="false" aria-controls="system-panel">
                                <i class="fa fa-desktop me-2"></i> System Logs
                            </button>
                        </h2>
                        <div id="system-panel" class="accordion-collapse collapse" aria-labelledby="system-heading" data-bs-parent="#profileForm">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Last Login</label>
                                        <input type="text" class="form-control" value="<?= safe_date($user['last_login']) ?>" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Registration Date</label>
                                        <input type="text" class="form-control" value="<?= safe_date($user['date_registered'], 'M d, Y') ?>" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status</label><br>
                                        <span class="status-badge-pro">
                                            <span class="status-dot"></span>
                                            <?= $isActive ? "Active" : "Inactive" ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Save/Cancel buttons -->
                    <div class="mt-4 d-flex justify-content-end gap-2" id="editActions" style="display:none;">
                        <button type="button" class="btn btn-secondary cancel-btn" id="cancelBtn"><i class="fa fa-times"></i> Cancel</button>
                        <button type="submit" class="btn btn-success save-btn px-4 shadow-sm"><i class="fa fa-save"></i> Save Changes</button>
                    </div>
                </form>
                <div id="formFeedback" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
<!-- Toast for feedback -->
<div class="toast align-items-center text-white border-0 position-fixed end-0 top-0 m-4" id="profileToast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index:1055; min-width:260px;">
    <div class="d-flex">
        <div class="toast-body" id="toastMsg"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('profilePicInput').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(ev){
            document.getElementById('profilePicPreview').src = ev.target.result;
        }
        reader.readAsDataURL(file);
        const formData = new FormData();
        formData.append('profile_picture', file);
        fetch('update_profile.php', { method: 'POST', body: formData })
        .then(res=>res.json())
        .then(data=>{
            showToast(data.status === 'success' ? 'success':'danger', data.message || 'Profile photo updated.');
            document.querySelectorAll('.user-icon img').forEach(img=>{
                img.src = (data.profile_picture || ev.target.result) + "?t=" + new Date().getTime();
            });
        });
    }
});
document.getElementById('profileForm').addEventListener('submit', function(e){
    e.preventDefault();
    let form = e.target; let formData = new FormData(form);
    fetch('update_profile.php', { method: 'POST', body: formData })
    .then(res=>res.json())
    .then(data=>{
        showToast(data.status === 'success' ? 'success':'danger', data.message || 'Profile updated!');
        if(data.status === 'success') setTimeout(()=>window.location.reload(),1200);
    }).catch(()=>{ showToast('danger', 'Error saving profile.'); });
});
function showToast(type, msg){
    const toast = document.getElementById('profileToast');
    const toastMsg = document.getElementById('toastMsg');
    toastMsg.innerHTML = (type === 'success' ? '<i class="fa fa-check-circle me-2"></i>':'<i class="fa fa-exclamation-circle me-2"></i>') + msg;
    toast.classList.remove('bg-success','bg-danger','bg-warning');
    toast.classList.add(type==='success'?'bg-success':(type==='warning'?'bg-warning':'bg-danger'));
    let bsToast = bootstrap.Toast.getOrCreateInstance(toast); bsToast.show();
}
const editBtn = document.getElementById('editBtn');
const cancelBtn = document.getElementById('cancelBtn');
const editActions = document.getElementById('editActions');
const form = document.getElementById('profileForm');
let origFormData = null;
function setFormEditable(editMode) {
    form.querySelectorAll('input, select, textarea').forEach(el=>{
        if (el.name !== 'role') el.disabled = !editMode;
    });
    editActions.style.display = editMode ? '' : 'none';
    editBtn.style.display = editMode ? 'none' : '';
}
editBtn.addEventListener('click', function(){
    origFormData = new FormData(form);
    setFormEditable(true);
    document.querySelectorAll('.accordion-collapse').forEach(p=>p.classList.add('show'));
});
cancelBtn.addEventListener('click', function(){
    if (origFormData) {
        origFormData.forEach((val, key)=>{
            let input = form.querySelector(`[name="${key}"]`);
            if(input) input.value = val;
        });
    }
    setFormEditable(false);
});
setFormEditable(false);
form.querySelectorAll('input[required], select[required]').forEach(input=>{
    input.addEventListener('input', function(){
        if(!input.checkValidity()){
            input.classList.add('is-invalid');
        }else{
            input.classList.remove('is-invalid');
        }
    });
});
</script>
</body>
</html>