<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['Guidance Admin','Counselor'], true)) { header('Location: login.php'); exit; }
if (!isset($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Guidance Calendar</title>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
  /* Ensure the calendar uses full width inside main-content */
  .container-fluid { max-width: 100%; }
  #calendar { min-height: calc(100vh - 160px); }
</style>
</head>
<body>
<?php include 'guidance_admin_header.php'; ?>
<div class="main-content">
  <div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Appointments Calendar</h3>
      <button class="btn btn-primary" id="btnNew" data-bs-toggle="modal" data-bs-target="#newApptModal">New Appointment</button>
    </div>
    <div id="calendar"></div>
  </div>
</div>
<!-- New Appointment Modal -->
<div class="modal fade" id="newApptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newApptForm">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <div class="mb-3">
            <label class="form-label">Student (type ID or pick)</label>
            <input class="form-control" name="student_id" list="studentsList" placeholder="e.g., 2023-0001" required>
            <datalist id="studentsList">
              <?php
              $students = $conn->query("SELECT user_id, TRIM(CONCAT(first_name,' ',last_name)) AS name FROM users WHERE role='Student' AND status='Active' ORDER BY name LIMIT 200");
              if ($students && $students->num_rows) {
                while($s = $students->fetch_assoc()){
                  echo '<option value="'.htmlspecialchars($s['user_id']).'">'.htmlspecialchars($s['name']).' ('.htmlspecialchars($s['user_id']).')</option>';
                }
              }
              ?>
            </datalist>
            <?php if (!$students || !$students->num_rows): ?>
              <small class="text-muted">No active students found. You can still type a student ID if known.</small>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="form-label">Counselor</label>
            <?php
            $cname = '';
            $me = $conn->prepare("SELECT TRIM(CONCAT(first_name,' ',last_name)) AS name FROM users WHERE user_id = ?");
            if ($me) { $me->bind_param('s', $_SESSION['user_id']); $me->execute(); $r=$me->get_result()->fetch_assoc(); if($r){ $cname=$r['name']; } }
            ?>
            <input type="hidden" name="counselor_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
            <input type="text" class="form-control" value="<?= htmlspecialchars($cname ?: $_SESSION['user_id']) ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Date & Time</label>
            <input type="datetime-local" class="form-control" name="datetime" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason (optional)</label>
            <textarea class="form-control" name="reason" rows="2" placeholder="Reason or notes..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveAppt">Create</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const modalEl = document.getElementById('newApptModal');
  const modal = new bootstrap.Modal(modalEl);
  const saveBtn = document.getElementById('saveAppt');
  // Attach handler first so it works even if calendar fails to load
  saveBtn.addEventListener('click', async ()=>{
    const form = document.getElementById('newApptForm');
    const studentId = form.elements['student_id'].value.trim();
    const counselorId = form.elements['counselor_id'].value;
    const datetime = form.elements['datetime'].value;
    const reason = form.elements['reason'].value.trim();
    if(!studentId){ alert('Please enter a valid student ID.'); return; }
    if(!counselorId){ alert('Counselor is not set.'); return; }
    if(!datetime){ alert('Please select date & time'); return; }
    // Disable button with spinner
    saveBtn.disabled = true;
    const originalHtml = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving';
    try {
      const body = new URLSearchParams();
      body.append('csrf_token', form.elements['csrf_token'].value);
      body.append('student_id', studentId);
      body.append('counselor_id', counselorId);
      body.append('datetime', datetime);
      body.append('reason', reason);
      const r = await fetch('admin_create_appointment.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
      let data;
      try { data = await r.json(); }
      catch (e) { data = { success:false, message: (await r.text()) || 'Server error' }; }
      alert(data.message || (data.success ? 'Saved' : 'Failed'));
      if (data.success) { modal.hide(); try { cal.refetchEvents(); } catch(_){} }
    } catch(err){
      alert('Network error');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalHtml;
    }
  });
  // Calendar init
  let cal;
  try {
    const calEl = document.getElementById('calendar');
    cal=new FullCalendar.Calendar(calEl,{
      initialView:'timeGridWeek', editable:true, eventOverlap:false, height:'auto',
      slotMinTime: '08:00:00', slotMaxTime: '17:00:00',
      businessHours: { daysOfWeek:[1,2,3,4,5], startTime:'08:00', endTime:'17:00' },
      nowIndicator:true,
      events:'guidance_calendar_admin_events.php',
      eventConstraint: 'businessHours',
      eventDrop:(info)=>update(info), eventResize:(info)=>update(info)
    });
    function update(info){
      const p=new URLSearchParams({ id: info.event.id, start: info.event.start.toISOString(), csrf_token: '<?= $_SESSION['csrf_token'] ?>' });
      fetch('admin_update_appointment.php',{ method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p })
        .then(r=>r.json()).then(d=>{ if(!d.success){ alert(d.message||'Update failed'); info.revert(); } else { cal.refetchEvents(); } });
    }
    cal.render();
    window.addEventListener('resize', ()=> cal.updateSize());
  } catch (e) {
    console.warn('Calendar failed to initialize:', e);
  }
  // Auto-open modal if ?new=1
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('new') === '1') {
      setTimeout(()=> modal.show(), 100);
    }
  } catch(_){}
});
</script>
</body>
</html>