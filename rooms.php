<?php
include 'config.php';
session_start();
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in to apply for a room."]);
    exit;
}
$user_id = $_SESSION['user_id'];
// Handle room application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_room'])) {
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    // Agreement gate: require latest active policy acceptance
    require_once __DIR__ . '/includes/DormAgreementService.php';
    $agreementService = new DormAgreementService($conn);
    $activeAgreement = $agreementService->getActiveAgreement();
    if ($activeAgreement && !$agreementService->hasUserAccepted($user_id, (int)$activeAgreement['id'])) {
        echo json_encode(["success" => false, "message" => "You must accept the latest Dormitory Agreement & Policy before applying."]);
        exit;
    }
    // Check if user already has an approved application
    $approvedQuery = "SELECT * FROM student_room_applications WHERE user_id = ? AND status = 'Approved'";
    $stmt = $conn->prepare($approvedQuery);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "You already have an approved application for a room."]);
        exit;
    }
    // Validate room existence
    $roomQuery = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($roomQuery);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Selected room does not exist."]);
        exit;
    }
    // Check if user already applied for the room
    $checkQuery = "SELECT * FROM student_room_applications WHERE user_id = ? AND room_id = ? AND status = 'Pending'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $user_id, $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $insertQuery = "INSERT INTO student_room_applications (user_id, room_id, status, applied_at, price_per_month) VALUES (?, ?, 'Pending', NOW(), (SELECT price_per_month FROM rooms WHERE id = ?))";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sii", $user_id, $room_id, $room_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Application submitted successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error submitting application."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "You already have a pending application for this room."]);
    }
    exit;
}
$query = "SELECT * FROM rooms";
$result = mysqli_query($conn, $query);
$rooms = [];
if ($result) { while ($r = mysqli_fetch_assoc($result)) { $rooms[] = $r; } }
// UI helpers
function peso($v){ return '₱' . number_format((float)$v, 2); }
$amenity_icons = [
    'wifi' => '<i class="fas fa-wifi" aria-hidden="true"></i>',
    'table' => '<i class="fas fa-table" aria-hidden="true"></i>',
    'aircon' => '<i class="fas fa-wind" aria-hidden="true"></i>',
    'ac' => '<i class="fas fa-wind" aria-hidden="true"></i>',
    'fan' => '<i class="fas fa-fan" aria-hidden="true"></i>',
    'tv' => '<i class="fas fa-tv" aria-hidden="true"></i>',
    'shower' => '<i class="fas fa-shower" aria-hidden="true"></i>',
    'locker' => '<i class="fas fa-lock" aria-hidden="true"></i>',
    'bed' => '<i class="fas fa-bed" aria-hidden="true"></i>',
    'lamp' => '<i class="fas fa-lightbulb" aria-hidden="true"></i>',
    'kitchen' => '<i class="fas fa-utensils" aria-hidden="true"></i>',
    'bathroom' => '<i class="fas fa-bath" aria-hidden="true"></i>',
    'window' => '<i class="fas fa-window-maximize" aria-hidden="true"></i>',
    'parking' => '<i class="fas fa-parking" aria-hidden="true"></i>',
    'balcony' => '<i class="fas fa-tree" aria-hidden="true"></i>',
];
function norm_amenity($raw){
    $r = strtolower(trim($raw));
    return match($r){ 'air-con','air con','a/c' => 'aircon', 'desk' => 'table', 'bath','cr','toilet' => 'bathroom', default => $r };
}
function render_amenities($amenities, $icons){
    if (!$amenities) return '<span class="text-muted">No amenities listed</span>';
    $out = '';
    foreach (preg_split('/[\s,;]+/', strtolower(trim($amenities))) as $raw){
        if (!$raw) continue;
        $key = norm_amenity($raw);
        $label = ucfirst($key);
        $icon = $icons[$key] ?? '<i class="fas fa-circle-dot" aria-hidden="true"></i>';
        $out .= '<span class="amenity-pill" title="'.$label.'" aria-label="'.$label.'">'.$icon.' '.$label.'</span>';
    }
    return $out ?: '<span class="text-muted">No amenities listed</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #003366;
            --accent: #F7C873;
            --glass: rgba(255,255,255,.82);
            --ink: #0f172a;
        }
        body { background: linear-gradient(135deg, #e7efff 0%, #f4f8fc 100%); font-family: 'Inter','Segoe UI','Roboto',Arial,sans-serif; }
        .grid { column-count: 3; column-gap: 2.2rem; }
        @media (max-width: 1100px){ .grid { column-count: 2; } }
        @media (max-width: 700px){ .grid { column-count: 1; } }
        .card.room-card {
            display: inline-block; width: 100%; margin-bottom: 2.2rem;
            background: var(--glass); border:1px solid rgba(0,0,0,.04);
            border-radius: 18px; box-shadow: 0 14px 44px rgba(2,32,71,.12);
            overflow:hidden; break-inside: avoid; transition: transform .35s, box-shadow .35s;
            opacity:1;
        }
        @keyframes fadeUp { from{ transform: translateY(36px); opacity:0 } to{ transform:none; opacity:1 } }
        .card.room-card:hover { transform: translateY(-8px) scale(1.01); box-shadow: 0 22px 66px rgba(2,32,71,.18); }
        .room-img { width:100%; height: 220px; object-fit: cover; }
        .price-chip, .status-chip { position:absolute; top:14px; z-index:2; padding:.42rem 1rem; border-radius: 999px; font-weight:800; box-shadow: 0 6px 16px rgba(0,0,0,.12); }
        .price-chip { right:16px; background: var(--accent); color:#5c4a00; }
        .status-chip { left:16px; background:#fff; color:#12b886; border:1px solid #12b886; }
        .status-chip.full { color:#e03131; border-color:#e03131; }
        .amenity-pill { background:#f3f6ff; color:var(--primary); border-radius: 999px; padding:.38rem .8rem; display:inline-flex; gap:.4rem; align-items:center; font-weight:700; box-shadow:0 2px 10px #f7c87331; margin:.15rem; transition: transform .16s, background .16s; }
        .amenity-pill:hover { transform: scale(1.04); background:var(--accent); color:#fff; }
        .btn-view { background:#fff; color:var(--primary); font-weight:800; border:2px solid var(--primary); border-radius:12px; padding:.6rem 1rem; }
        .btn-view:hover { background:var(--primary); color:#fff; border-color:var(--accent); }
        .btn-apply { background:var(--primary); color:#fff; font-weight:800; border-radius:12px; padding:.6rem 1rem; }
        .btn-apply:disabled { background:#bbb; cursor:not-allowed; }
        .modal-header { background:var(--primary); color:#fff; border-top-left-radius:16px; border-top-right-radius:16px; }
        .modal-content { border-radius:16px; box-shadow:0 16px 44px rgba(2,32,71,.18); }
        @media (prefers-reduced-motion: no-preference) {
            .card.room-card { opacity:0; animation: fadeUp .7s cubic-bezier(.19,1,.22,1) forwards; }
            .grid > .room-card:nth-child(2) { animation-delay: .08s; }
            .grid > .room-card:nth-child(3) { animation-delay: .12s; }
            .grid > .room-card:nth-child(4) { animation-delay: .16s; }
        }
    </style>
    <title>Apply for Dormitory Room</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    <div class="container mt-4">
        
        <h2 class="text-center mb-3" style="color:var(--primary); font-weight:900; letter-spacing:.4px;">Premium Dorm Rooms</h2>
        <!-- Controls -->
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input id="searchRooms" class="form-control" style="min-width:260px; border-radius:12px;" type="search" placeholder="Search rooms...">
                <select id="sortRooms" class="form-select" style="border-radius:12px;">
                    <option value="">Sort by</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="avail_desc">Availability</option>
                </select>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filterAvail">
                    <label class="form-check-label" for="filterAvail">Available only</label>
                </div>
                <select id="amenityFilter" class="form-select" style="border-radius:12px;">
                    <option value="">Amenity: Any</option>
                    <option value="wifi">WiFi</option>
                    <option value="aircon">Aircon</option>
                    <option value="bathroom">Bathroom</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="locker">Locker</option>
                    <option value="tv">TV</option>
                </select>
            </div>
        </div>
        <?php if (!count($rooms)): ?>
        <div class="text-center" style="color:#64748b; padding:40px 0;">
            <div style="font-size:56px; opacity:.8; margin-bottom:12px;"><i class="fa-solid fa-bed"></i></div>
            <div style="font-weight:800; font-size:20px; color:#0f172a;">No rooms found</div>
            <div style="margin-top:6px;">Please check back later or contact the dormitory admin.</div>
        </div>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($rooms as $row): 
                $available = max(0, (int)$row['total_beds'] - (int)$row['occupied_beds']);
                $isAvail = $available > 0;
                $statusClass = $isAvail ? 'status-chip' : 'status-chip full';
                $amenStr = strtolower(trim($row['amenities'] ?? ''));
                $price = (float)$row['price_per_month'];
            ?>
           
            <div class="card room-card" data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>" data-amenities="<?= htmlspecialchars($amenStr) ?>" data-available="<?= $isAvail ? '1':'0' ?>" data-price="<?= $price ?>">
                <div style="position:relative;">
                   
                    <div class="skeleton" style="position:absolute; inset:0; background:linear-gradient(90deg, #eef2f7 25%, #f5f7fb 37%, #eef2f7 63%); background-size:400% 100%; animation: shimmer 1.2s infinite; border-bottom-left-radius:18px; border-bottom-right-radius:18px;"></div>
                    <img src="<?= htmlspecialchars($row['image']) ?>" class="room-img" alt="Room image: <?= htmlspecialchars($row['name']) ?>" loading="lazy" width="1024" height="680" onload="this.previousElementSibling.remove()" onerror="this.previousElementSibling.style.background='#e9ecef'; this.alt='Image unavailable';">
                    <span class="price-chip"><?= peso($row['price_per_month']) ?>/month</span>
                    <span class="<?= $statusClass ?>"><?= $isAvail ? '✅ Available' : '❌ Full' ?></span>
                </div>
                <div class="card-body">
                    <h5 class="card-title" style="font-weight:900;color:var(--ink)"><?= htmlspecialchars($row['name']) ?></h5>
                    <div class="d-flex align-items-center gap-3 mb-2" role="list">
                        <span class="text-primary" aria-hidden="true"><i class="fas fa-bed"></i></span>
                        <span role="listitem"><?= (int)$row['total_beds'] ?> Beds</span>
                        <span class="text-primary" aria-hidden="true"><i class="fas fa-user-check"></i></span>
                        <span role="listitem"><?= $available ?> Available</span>
                    </div>
                    <div class="amenities-list">
                        <?= render_amenities($row['amenities'] ?? '', $amenity_icons) ?>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button class="btn-view flex-fill view-details-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#roomDetailsModal"
                                data-room-name="<?= htmlspecialchars($row['name']) ?>"
                                data-image="<?= htmlspecialchars($row['image']) ?>"
                                data-total-beds="<?= (int)$row['total_beds'] ?>"
                                data-occupied-beds="<?= (int)$row['occupied_beds'] ?>"
                                data-price="<?= peso($row['price_per_month']) ?>"
                                data-amenities="<?= htmlspecialchars($row['amenities'] ?? '') ?>"
                        >View Details</button>
                        <button class="btn-apply flex-fill apply-room-btn" <?= $isAvail ? '' : 'disabled' ?> data-room-id="<?= (int)$row['id'] ?>">Apply</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- Room Details Modal -->
    <div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Room Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 id="roomName" class="room-name"></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <img src="" class="img-fluid mb-3" id="roomImage" alt="Room Image" style="border-radius:12px; box-shadow:0 10px 30px rgba(2,32,71,.18);">
                        </div>
                        <div class="col-md-6">
                            <p>Total Beds: <span id="totalBeds"></span></p>
                            <p>Occupied Beds: <span id="occupiedBeds"></span></p>
                            <p>Available Beds: <span id="availableBeds"></span></p>
                            <p>Price per Month: ₱<span id="price"></span></p>
                            <p>Amenities: <span id="amenities"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Apply for Room Modal -->
    <div class="modal fade" id="applyRoomModal" tabindex="-1" aria-labelledby="applyRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applyRoomForm">
                        <input type="hidden" name="room_id" id="room_id">
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Agreement Modal (shown before apply if not yet accepted) -->
    <div class="modal fade" id="agreementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agreementTitle">Dormitory Agreement & Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="agreementContent" style="max-height: 420px; overflow-y: auto; border: 1px solid #dee2e6; padding: 12px; border-radius: 8px;"></div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="agreeCheckbox">
                        <label class="form-check-label" for="agreeCheckbox">I have read and agree to the Dormitory Agreement & Policy</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="downloadAgreementLink" class="btn btn-outline-secondary" target="_blank">Download PDF</a>
                    <button type="button" class="btn btn-primary" id="agreeBtn" disabled>I Agree</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Confirmation Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                    <!-- Message content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div id="toastWrap" aria-live="polite" aria-atomic="true" style="position: fixed; right: 16px; bottom: 16px; z-index: 1080;"></div>
    <script>
        function showToast(type, msg){
            const wrap = document.getElementById('toastWrap');
            const id = 't'+Date.now();
            const bg = type==='success' ? '#d1e7dd' : '#f8d7da';
            const fg = type==='success' ? '#0f5132' : '#842029';
            const icon = type==='success' ? 'fa-circle-check' : 'fa-circle-exclamation';
            const el = document.createElement('div');
            el.id = id;
            el.setAttribute('role','alert');
            el.style.cssText = `background:${bg}; color:${fg}; box-shadow:0 10px 26px rgba(2,32,71,.22); border-radius:12px; padding:10px 14px; margin-top:10px; display:flex; align-items:center; gap:10px;`;
            el.innerHTML = `<i class="fa-solid ${icon}"></i><div>${msg}</div>`;
            wrap.appendChild(el);
            setTimeout(()=>{ el.style.opacity='0'; el.style.transform='translateY(6px)'; setTimeout(()=>el.remove(), 220); }, 2200);
        }
        document.addEventListener("DOMContentLoaded", function() {
            let applyRoomModal = new bootstrap.Modal(document.getElementById("applyRoomModal"));
            let roomDetailsModal = new bootstrap.Modal(document.getElementById("roomDetailsModal"));
            let messageModal = new bootstrap.Modal(document.getElementById("messageModal"));
            let agreementModal = new bootstrap.Modal(document.getElementById("agreementModal"));
            function escapeHtml(str){
                return str.replace(/[&<>"]/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]); });
            }
            document.querySelectorAll(".view-details-btn").forEach(button => {
                button.addEventListener("click", function() {
                    document.getElementById("roomImage").src = this.dataset.image;
                    document.getElementById("roomName").textContent = this.dataset.roomName;
                    document.getElementById("totalBeds").textContent = this.dataset.totalBeds;
                    document.getElementById("occupiedBeds").textContent = this.dataset.occupiedBeds;
                    document.getElementById("availableBeds").textContent = this.dataset.totalBeds - this.dataset.occupiedBeds;
                    document.getElementById("price").textContent = this.dataset.price;
                    const amenities = this.dataset.amenities || '';
                    document.getElementById("amenities").textContent = amenities || 'No amenities listed';
                    roomDetailsModal.show();
                });
            });
            document.querySelectorAll(".apply-room-btn").forEach(button => {
                button.addEventListener("click", function() {
                    document.getElementById("room_id").value = this.getAttribute("data-room-id");
                    fetch("check_dorm_agreement.php")
                        .then(r => r.json())
                        .then(data => {
                            if (data.success && (data.accepted || !data.hasActive)) {
                                applyRoomModal.show();
                            } else if (data.success && data.hasActive && !data.accepted) {
                                document.getElementById("agreementTitle").textContent = data.agreement.title;
                                document.getElementById("agreementContent").innerHTML = escapeHtml(data.agreement.content).replace(/\n/g, '<br>');
                                document.getElementById("agreeCheckbox").checked = false;
                                document.getElementById("agreeBtn").disabled = true;
                                document.getElementById("downloadAgreementLink").href = "download_dorm_agreement.php?id=" + data.agreement.id;
                                agreementModal.show();
                            } else {
                                applyRoomModal.show();
                            }
                        })
                        .catch(() => applyRoomModal.show());
                });
            });
            document.getElementById("agreeCheckbox").addEventListener("change", function(){
                document.getElementById("agreeBtn").disabled = !this.checked;
            });
            document.getElementById("agreeBtn").addEventListener("click", function(){
                const btn = this;
                btn.disabled = true;
                fetch('dorm_agreement_accept.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'accept' }) })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            agreementModal.hide();
                            applyRoomModal.show();
                        } else {
                            document.getElementById("messageModalBody").classList.add("error");
                            document.getElementById("messageModalBody").classList.remove("success");
                            document.getElementById("messageModalBody").innerHTML = data.message || 'Failed to record acceptance.';
                            messageModal.show();
                        }
                        btn.disabled = false;
                    })
                    .catch(() => {
                        document.getElementById("messageModalBody").classList.add("error");
                        document.getElementById("messageModalBody").classList.remove("success");
                        document.getElementById("messageModalBody").innerHTML = 'Network error. Please try again.';
                        messageModal.show();
                        btn.disabled = false;
                    });
            });
            document.getElementById("applyRoomForm").addEventListener("submit", function(event) {
                event.preventDefault();
                let formData = new FormData(this);
                formData.append("apply_room", true);
                const submitBtn = this.querySelector('button[type="submit"]');
                const oldHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting';
                fetch("rooms.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    applyRoomModal.hide();
                    showToast(data.success ? 'success' : 'error', data.message || (data.success?'Success':'Something went wrong'));
                    if (data.success) setTimeout(()=>location.reload(), 1200);
                })
                .catch(() => { showToast('error','Network error. Please try again.'); })
                .finally(()=>{ submitBtn.disabled=false; submitBtn.innerHTML = oldHtml; });
            });

            // Instant filters & sort
            const search = document.getElementById('searchRooms');
            const sort = document.getElementById('sortRooms');
            const amen = document.getElementById('amenityFilter');
            const onlyAvail = document.getElementById('filterAvail');
            const cards = Array.from(document.querySelectorAll('.grid .room-card'));

            function applyFilters(){
                const q = (search.value||'').trim().toLowerCase();
                const a = amen.value;
                const avail = onlyAvail.checked;
                let filtered = cards;
                filtered.forEach(c=>{
                    const name = c.dataset.name||'';
                    const ams = c.dataset.amenities||'';
                    const av = c.dataset.available==='1';
                    let ok = true;
                    if (q && !name.includes(q)) ok=false;
                    if (a && !ams.includes(a)) ok=false;
                    if (avail && !av) ok=false;
                    c.style.display = ok ? 'inline-block' : 'none';
                });
                applySort();
            }

            function applySort(){
                const v = sort.value;
                if (!v) return;
                const grid = document.querySelector('.grid');
                const visible = cards.filter(c=>c.style.display!== 'none');
                visible.sort((c1,c2)=>{
                    const p1 = parseFloat(c1.dataset.price||'0');
                    const p2 = parseFloat(c2.dataset.price||'0');
                    const a1 = c1.dataset.available==='1'?1:0;
                    const a2 = c2.dataset.available==='1'?1:0;
                    if (v==='price_asc') return p1 - p2;
                    if (v==='price_desc') return p2 - p1;
                    if (v==='avail_desc') return a2 - a1;
                    return 0;
                });
                visible.forEach(c=> grid.appendChild(c));
            }

            [search, sort, amen, onlyAvail].forEach(el=> el && el.addEventListener('input', applyFilters));
            [sort].forEach(el=> el && el.addEventListener('change', applySort));
            applyFilters();
        });
    </script>
    <style>
        @keyframes shimmer { 0%{ background-position: 0 0 } 100%{ background-position: -400% 0 } }
    </style>
</body>
</html>