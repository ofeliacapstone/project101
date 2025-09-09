<?php
session_start();
require_once 'config.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Fetch user details for application form
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();
// Fetch scholarships (tolerant to minor data issues)
$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM scholarship_applications sa WHERE sa.scholarship_id = s.id) AS applicant_count
        FROM scholarships s
        WHERE LOWER(TRIM(s.status)) = 'active'
          AND (s.deadline IS NULL OR s.deadline >= CURDATE())
        ORDER BY s.deadline ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Scholarships - NEUST Gabaldon</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/student_theme.css">
</head>
<body>
<?php include('student_header.php'); ?>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-graduation-cap"></i> Available Scholarships</h1>
        <p>Discover and apply for scholarships that match your academic excellence and financial needs</p>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number"><?= $result ? $result->num_rows : 0 ?></div>
                <div class="stats-label">Available Scholarships</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $pending_sql = "SELECT COUNT(*) as count FROM scholarship_applications WHERE user_id = ? AND status = 'pending'";
                    $pending_stmt = $conn->prepare($pending_sql);
                    $pending_stmt->bind_param("s", $user_id);
                    $pending_stmt->execute();
                    $pending_result = $pending_stmt->get_result();
                    $pending_count = $pending_result->fetch_assoc()['count'] ?? 0;
                    $pending_stmt->close();
                    echo (int)$pending_count;
                    ?>
                </div>
                <div class="stats-label">Pending Applications</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $approved_sql = "SELECT COUNT(*) as count FROM scholarship_applications WHERE user_id = ? AND status = 'approved'";
                    $approved_stmt = $conn->prepare($approved_sql);
                    $approved_stmt->bind_param("s", $user_id);
                    $approved_stmt->execute();
                    $approved_result = $approved_stmt->get_result();
                    $approved_count = $approved_result->fetch_assoc()['count'] ?? 0;
                    $approved_stmt->close();
                    echo (int)$approved_count;
                    ?>
                </div>
                <div class="stats-label">Approved Scholarships</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $total_sql = "SELECT COUNT(*) as count FROM scholarship_applications WHERE user_id = ?";
                    $total_stmt = $conn->prepare($total_sql);
                    $total_stmt->bind_param("s", $user_id);
                    $total_stmt->execute();
                    $total_result = $total_stmt->get_result();
                    $total_count = $total_result->fetch_assoc()['count'] ?? 0;
                    $total_stmt->close();
                    echo (int)$total_count;
                    ?>
                </div>
                <div class="stats-label">Total Applications</div>
            </div>
        </div>
    </div>
    
    <div class="filters-section">
        <div class="row">
            <div class="col-md-4">
                <div class="filter-group">
                    <label for="searchInput"><i class="fas fa-search"></i> Search Scholarships</label>
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="Search by name, type, or description...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label for="typeFilter"><i class="fas fa-filter"></i> Scholarship Type</label>
                    <select id="typeFilter" class="form-control">
                        <option value="">All Types</option>
                        <option value="Academic">Academic</option>
                        <option value="Leadership">Leadership</option>
                        <option value="Need-based">Need-based</option>
                        <option value="Sports">Sports</option>
                        <option value="Arts">Arts</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label for="sortFilter"><i class="fas fa-sort"></i> Sort By</label>
                    <select id="sortFilter" class="form-control">
                        <option value="deadline">Deadline (Earliest)</option>
                        <option value="deadline_desc">Deadline (Latest)</option>
                        <option value="amount">Amount (Highest)</option>
                        <option value="amount_asc">Amount (Lowest)</option>
                        <option value="popularity">Popularity</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button id="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="scholarshipsGrid" class="row">
        <div id="noResults" class="col-12 d-none">
            <div class="no-scholarships">
                <i class="fas fa-search"></i>
                <h4>No matching scholarships</h4>
                <p>Try different filters or search terms.</p>
            </div>
        </div>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $rawDeadline = $row['deadline'] ?? '';
                    $deadlineIso = '';
                    $deadlineDisplay = '—';
                    $parsed = null;
                    foreach ([['Y-m-d','M j, Y'], ['m/d/Y','M j, Y'], ['m/d/y','M j, Y']] as $fmt) {
                        $try = DateTime::createFromFormat($fmt[0], $rawDeadline);
                        if ($try && $try->format($fmt[0]) === $rawDeadline) { $parsed = $try; break; }
                    }
                    if ($parsed) { $deadlineIso = $parsed->format('Y-m-d'); $deadlineDisplay = $parsed->format('M j, Y'); }
                ?>
                <div class="col-lg-4 col-md-6 scholarship-item" 
                     data-name="<?= strtolower($row['name']) ?>"
                     data-type="<?= strtolower($row['type']) ?>"
                     data-amount="<?= (float)$row['amount'] ?>"
                     data-deadline="<?= htmlspecialchars($row['deadline'] ?? '') ?>"
                     data-deadline="<?= htmlspecialchars($deadlineIso) ?>"
                     data-popularity="<?= (int)$row['applicant_count'] ?>">

                    <div class="scholarship-card">
                        <div class="card-header">
                            <div class="scholarship-type"><?= htmlspecialchars($row['type']) ?></div>
                            <h5 class="mb-0"><?= htmlspecialchars($row['name']) ?></h5>
                        </div>
                        
                        <div class="card-body">
                            <?php if ((float)$row['amount'] > 0): ?>
                                <div class="amount-badge mb-2">
                                    <i class="fas fa-peso-sign"></i> <?= number_format((float)$row['amount'], 2) ?>
                                </div>
                            <?php endif; ?>
                            
                            <p class="text-muted mb-3"><?= htmlspecialchars(mb_strimwidth($row['description'], 0, 100, '...')) ?></p>

                            <div class="scholarship-info">
                                <i class="fas fa-calendar-alt"></i>
                                <span>
                                    Deadline: 
                                    <?php 
                                    $dl = $row['deadline'] ?? null; 
                                    if ($dl && $dl !== '0000-00-00' && strtotime($dl)) { 
                                        echo date("M j, Y", strtotime($dl));
                                    } else { echo '—'; }
                                    ?>
                                </span>
                                <span>Deadline: <?= htmlspecialchars($deadlineDisplay) ?></span>
                            </div>

                            <div class="scholarship-info">
                                <i class="fas fa-users"></i>
                                <span><?= (int)$row['applicant_count'] ?> applicants</span>
                            </div>
                            
                            <?php
                            if (!empty($row['deadline'])) {
                                $days_until_deadline = (strtotime($row['deadline']) - time()) / (60 * 60 * 24);
                                if ($days_until_deadline <= 7 && $days_until_deadline > 0):
                            ?>
                                <div class="deadline-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Only <?= ceil($days_until_deadline) ?> days left!
                                </div>
                            <?php 
                                endif;
                            } 
                            ?>
                            
                            <button class="btn btn-apply apply-btn" 
                                    data-id="<?= (int)$row['id'] ?>" 
                                    data-name="<?= htmlspecialchars($row['name']) ?>">
                                <i class="fas fa-paper-plane"></i> Apply Now
                            </button>
                            
                            <button class="btn btn-details view-details" 
                                    data-id="<?= (int)$row['id'] ?>">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="no-scholarships">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>No scholarships available at the moment</h4>
                    <p>Please check back later for new scholarship opportunities.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="scholarshipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--neust-blue); color: var(--neust-white);">
                <h5 class="modal-title" id="scholarshipTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scholarshipDetails"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyFromModal" aria-label="Open application form">
                    <i class="fas fa-paper-plane"></i> Apply Now
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--neust-blue); color: var(--neust-white);">
                <h5 class="modal-title">Scholarship Application Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="applicationForm"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    let currentScholarshipId = null;
    function parseDateYMD(value){
        if (!value || typeof value !== 'string') return null;
        if (value === '0000-00-00') return null;
        const m = /^([0-9]{4})-([0-9]{2})-([0-9]{2})$/.exec(value.trim());
        if (!m) {
            const d = new Date(value);
            return isNaN(d) ? null : d;
        }
        const y = parseInt(m[1],10), mo = parseInt(m[2],10)-1, da = parseInt(m[3],10);
        const d = new Date(y, mo, da);
        return isNaN(d) ? null : d;
    }
    function formatDeadline(value){
        const d = parseDateYMD(value);
        if (!d) return '—';
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }
    function filterScholarships() {
        const searchTerm = ($('#searchInput').val() || '').toLowerCase();
        const typeFilter = ($('#typeFilter').val() || '').toLowerCase();
        const sortBy = $('#sortFilter').val();
        $('.scholarship-item').each(function() {
            const $item = $(this);
            const name = ($item.data('name') || '').toString();
            const type = ($item.data('type') || '').toString();
            let show = true;
            if (searchTerm && !name.includes(searchTerm)) show = false;
            if (typeFilter && type !== typeFilter) show = false;
            $item.toggle(show);
        });
        const $visible = $('.scholarship-item:visible');
        $('#noResults').toggleClass('d-none', $visible.length > 0);
        const items = $visible.get();
        items.sort(function(a, b) {
            const safeDate = d => {
                const parsed = parseDateYMD(d);
                return parsed ? parsed : new Date(8640000000000000);
            };
            const $a = $(a), $b = $(b);
            switch(sortBy) {
                case 'deadline':      return safeDate($a.data('deadline')) - safeDate($b.data('deadline'));
                case 'deadline_desc': return safeDate($b.data('deadline')) - safeDate($a.data('deadline'));
                case 'amount':        return ($b.data('amount')||0) - ($a.data('amount')||0);
                case 'amount_asc':    return ($a.data('amount')||0) - ($b.data('amount')||0);
                case 'popularity':    return ($b.data('popularity')||0) - ($a.data('popularity')||0);
                default: return 0;
            }
        });
        $('#scholarshipsGrid').append(items);
    }
    $('#searchInput, #typeFilter, #sortFilter').on('change keyup', filterScholarships);
    $('#resetFilters').click(function() {
        $('#searchInput').val('');
        $('#typeFilter').val('');
        $('#sortFilter').val('deadline');
        filterScholarships();
    });
    // Initial filter apply to set no-results state correctly
    filterScholarships();
    $('.view-details').click(function() {
        const id = $(this).data('id');
        currentScholarshipId = id;
        $('#scholarshipTitle').text('Loading...');
        $('#scholarshipDetails').html('<div class="py-4 text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-3 text-muted">Fetching scholarship details...</div></div>');
        $('#scholarshipModal').modal('show');
        $.ajax({
            url: 'get_scholarship.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                try {
                    const data = (typeof response === 'string') ? JSON.parse(response) : response;
                    if (data.error) { $('#scholarshipDetails').html('<div class="alert alert-danger">'+data.error+'</div>'); return; }
                    $('#scholarshipTitle').text(data.name);
                    $('#scholarshipDetails').html(`
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-primary">Description</h6>
                                <p>${(data.description||'').toString()}</p>
                                <h6 class="text-primary">Eligibility Requirements</h6>
                                <p>${(data.eligibility||'').toString()}</p>
                                <h6 class="text-primary">Scholarship Requirements</h6>
                                <p>${(data.requirements || 'No specific requirements listed.').toString()}</p>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-primary">Quick Info</h6>
                                        <p><strong>Type:</strong> ${data.type}</p>
                                        <p><strong>Amount:</strong> ₱${parseFloat(data.amount||0).toLocaleString()}</p>
                                        <p><strong>Deadline:</strong> ${data.deadline_display || formatDeadline(data.deadline_iso || data.deadline)}</p>
                                        <p><strong>Max Applicants:</strong> ${data.max_applicants || 'Unlimited'}</p>
                                        <p><strong>Current Applicants:</strong> ${data.current_applicants || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#scholarshipModal').modal('show');
                } catch (e) {
                    $('#scholarshipDetails').html('<div class="alert alert-danger">Error parsing scholarship data.</div>');
                }
            },
            error: function() {
                $('#scholarshipDetails').html('<div class="alert alert-danger">Error loading scholarship details.</div>');
            }
        });
    });
    // Apply functionality (GET via querystring so load_application_form.php receives scholarship_id correctly)
    $('.apply-btn, #applyFromModal').off('click').on('click', function() {
        const scholarshipId = currentScholarshipId || $(this).data('id');
        currentScholarshipId = scholarshipId;
        $('#applicationForm').html('<div class="py-5 text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-3 text-muted">Loading application form...</div></div>');
        $('#applicationModal').modal('show');
        $('#applicationForm').load('load_application_form.php?scholarship_id=' + encodeURIComponent(scholarshipId), function() {
            $('#applicationModal').modal('show');
            $('#scholarshipModal').modal('hide');
        });
    });
});
</script>
<script src="assets/js/scholarships.js" defer></script>
</body>
</html>