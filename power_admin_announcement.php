<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') { header('Location: login.php'); exit(); }

$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$uploadDir = __DIR__ . "/uploads/announcements";
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $title = trim($_POST['title'] ?? '');
        $title = ($title !== '') ? $title : 'No Title';
        $webDir = 'uploads/announcements/';
        $orig = basename($_FILES['image']['name'] ?? '');
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if ($orig && in_array($ext, $allowed, true)) {
            $safeName = sprintf('%d_%s.%s', time(), bin2hex(random_bytes(4)), $ext);
            $dest = $uploadDir . '/' . $safeName;
            if (@move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $stmt = $conn->prepare("INSERT INTO announcements (title, image) VALUES (?, ?)");
                $stmt->bind_param('ss', $title, $safeName);
                if ($stmt->execute()) { $message = 'Announcement added successfully!'; } else { $message = 'Error saving to database!'; }
                $stmt->close();
            } else {
                $message = 'Error uploading file!';
            }
        } else {
            $message = 'Invalid file type! Please upload JPG, JPEG, PNG, or GIF.';
        }
    }

    if (isset($_POST['delete'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("SELECT image FROM announcements WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        
        if ($row) {
            @unlink($uploadDir . "/" . $row['image']);
            $del = $conn->prepare("DELETE FROM announcements WHERE id = ?");
            $del->bind_param('i', $id);
            if ($del->execute()) {
                $message = "Announcement deleted successfully!";
            } else {
                $message = "Error deleting announcement!";
            }
            $del->close();
        }
    }

    if (isset($_POST['update'])) {
        $id = (int)($_POST['id'] ?? 0);
        $title = !empty($_POST['title']) ? $_POST['title'] : "No Title";
        $fileName = basename($_FILES["image"]["name"] ?? '');
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!empty($fileName)) {
            $allowedTypes = array("jpg", "jpeg", "png", "gif");
            if (in_array($fileType, $allowedTypes)) {
                $safeName = sprintf('%d_%s.%s', time(), bin2hex(random_bytes(4)), $fileType);
                $dest = $uploadDir . '/' . $safeName;
                if (@move_uploaded_file($_FILES["image"]["tmp_name"], $dest)) {
                    $qs = $conn->prepare("SELECT image FROM announcements WHERE id = ?");
                    $qs->bind_param('i', $id);
                    $qs->execute();
                    $res2 = $qs->get_result();
                    $row = $res2 ? $res2->fetch_assoc() : null;
                    $qs->close();
                    if ($row && !empty($row['image'])) { @unlink($uploadDir . "/" . $row['image']); }
                    $upd = $conn->prepare("UPDATE announcements SET title = ?, image = ? WHERE id = ?");
                    $upd->bind_param('ssi', $title, $safeName, $id);
                    if ($upd->execute()) {
                        $message = "Announcement updated successfully!";
                    } else {
                        $message = "Error updating announcement!";
                    }
                    $upd->close();
                } else {
                    $message = "Error uploading file!";
                }
            } else {
                $message = "Invalid file type! Please upload JPG, JPEG, PNG, or GIF.";
            }
        } else {
            $upd = $conn->prepare("UPDATE announcements SET title = ? WHERE id = ?");
            $upd->bind_param('si', $title, $id);
            if ($upd->execute()) {
                $message = "Announcement updated successfully!";
            } else {
                $message = "Error updating announcement!";
            }
            $upd->close();
        }
    }

    if (isset($_POST['bulk_delete'])) {
        $ids = $_POST['ids'] ?? [];
        $deleted = 0;
        if (is_array($ids)) {
            foreach ($ids as $rawId) {
                $id = (int)$rawId;
                if ($id <= 0) { continue; }
                $stmt = $conn->prepare("SELECT image FROM announcements WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res ? $res->fetch_assoc() : null;
                $stmt->close();
                if ($row && !empty($row['image'])) { @unlink($uploadDir . "/" . $row['image']); }
                $del = $conn->prepare("DELETE FROM announcements WHERE id = ?");
                $del->bind_param('i', $id);
                if ($del->execute()) { $deleted++; }
                $del->close();
            }
        }
        $message = $deleted > 0 ? ("Deleted ".$deleted." announcement(s).") : "No announcements deleted.";
    }
}

$sql = "SELECT * FROM announcements ORDER BY date_posted DESC";
$result = $conn->query($sql);
$annCount = 0;
if ($cRes = $conn->query("SELECT COUNT(*) c FROM announcements")) { $annCount = (int)($cRes->fetch_assoc()['c'] ?? 0); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Admin · Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/admin_theme.css">
    <style>
        .dropzone{border:2px dashed var(--border); border-radius:12px; padding:16px; display:flex; align-items:center; gap:10px; background:#fff}
        .dropzone.dragover{background:#f9fbff; border-color:#60a5fa}
        .ann-grid{display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px}
        .ann-card{background:#fff; border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 6px 14px rgba(0,0,0,.06)}
        .ann-card img{width:100%; height:160px; object-fit:cover; display:block}
        .ann-card .body{padding:12px}
        .ann-card .title{font-weight:700; margin:0 0 6px; color:var(--brand)}
        .ann-card .meta{color:var(--muted); font-size:12px; margin-bottom:8px}
        .ann-card .actions{display:flex; gap:8px}
        .toolbar{display:flex; gap:10px; align-items:center; justify-content:space-between; margin:12px 0}
        .pill{display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid var(--border); border-radius:999px; background:#fff}
        .toast{position:fixed; right:16px; top:80px; background:#002147; color:#fff; padding:10px 14px; border-radius:8px; box-shadow:0 8px 20px rgba(0,0,0,.15); opacity:0; transform:translateY(-6px); transition:opacity .25s ease, transform .25s ease}
        .toast.show{opacity:1; transform:translateY(0)}
        @media (max-width:600px){ .dropzone{flex-direction:column; align-items:flex-start} }
        .filters{display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin:12px 0}
        .pagination{display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:12px}
        .lightbox{position:fixed; inset:0; background:rgba(0,0,0,.8); display:none; align-items:center; justify-content:center; z-index:50}
        .lightbox.open{display:flex}
        .lightbox img{max-width:90vw; max-height:80vh; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,.5)}
        .lb-btn{position:absolute; top:50%; transform:translateY(-50%); color:#fff; background:rgba(0,0,0,.3); border:none; font-size:28px; padding:8px 12px; cursor:pointer}
        .lb-prev{left:20px}
        .lb-next{right:20px}
        .lb-close{position:absolute; top:20px; right:20px; font-size:24px; background:rgba(0,0,0,.3); border:none; color:#fff; padding:6px 10px; cursor:pointer}
    </style>
</head>
<body>
<?php include 'power_admin_header.php'; ?>
    <main class="main">
        <div class="card">
            <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
            <div class="toolbar">
                <div class="pill"><i class="fa-regular fa-bell"></i> <strong><?= $annCount ?></strong> total</div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input class="input" id="searchAnnouncements" type="search" placeholder="Search announcements…">
                </div>
            </div>
            <div class="filters">
                <label>Sort
                    <select class="input" id="sortSelect">
                        <option value="date_desc">Newest first</option>
                        <option value="date_asc">Oldest first</option>
                        <option value="title_asc">Title A–Z</option>
                        <option value="title_desc">Title Z–A</option>
                    </select>
                </label>
                <label>From <input class="input" type="date" id="dateFrom"></label>
                <label>To <input class="input" type="date" id="dateTo"></label>
                <label>Per page
                    <select class="input" id="perPage">
                        <option>6</option>
                        <option selected>12</option>
                        <option>24</option>
                        <option>48</option>
                    </select>
                </label>
                <form id="bulkForm" method="POST" style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                    <input type="hidden" name="bulk_delete" value="1">
                    <div id="bulkIds"></div>
                    <button id="bulkDeleteBtn" class="btn" type="submit" disabled onclick="return confirm('Delete selected announcements?');">Bulk Delete</button>
                </form>
            </div>
            <form id="annForm" method="POST" enctype="multipart/form-data" style="margin:10px 0;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <input class="input" type="text" name="title" placeholder="Title" required>
                <div class="dropzone" id="dropzone">
                    <i class="fa-solid fa-image"></i>
                    <span>Drag & drop image here or click</span>
                    <input class="input" style="display:none" type="file" id="fileInput" name="image" accept="image/*" required>
                </div>
                <button class="btn" type="submit" name="add">Add</button>
            </form>
            <div id="preview" style="display:none; margin:8px 0"></div>
            <div class="ann-grid" id="annGrid">
                <?php while ($row = $result->fetch_assoc()): $t = trim($row['title']); ?>
                <div class="ann-card" data-title="<?= htmlspecialchars(mb_strtolower($t)) ?>" data-date="<?= htmlspecialchars(substr($row['date_posted'],0,10)) ?>" data-id="<?= (int)$row['id'] ?>">
                    <img class="lb" src="uploads/announcements/<?= htmlspecialchars($row['image']) ?>" alt="" loading="lazy">
                    <div class="body">
                        <label style="display:flex; align-items:center; gap:6px; margin-bottom:6px;"><input type="checkbox" class="sel"> Select</label>
                        <div class="title"><?= htmlspecialchars($row['title']) ?></div>
                        <div class="meta">Posted: <?= htmlspecialchars($row['date_posted']) ?></div>
                        <div class="actions">
                            <form method="POST" enctype="multipart/form-data" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                <input class="input" type="text" name="title" placeholder="New title">
                                <input class="input" type="file" name="image" accept="image/*">
                                <button class="btn btn-ghost" type="submit" name="update">Update</button>
                            </form>
                            <form method="POST" style="display:flex;" onsubmit="return confirm('Delete this announcement?');">
                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                <button class="btn" style="background:#dc2626" type="submit" name="delete">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="pagination">
                <button class="btn btn-ghost" id="prevPage">Prev</button>
                <span id="pageInfo" class="muted"></span>
                <button class="btn btn-ghost" id="nextPage">Next</button>
            </div>
        </div>
    </main>
    <div id="toast" class="toast<?= !empty($message) ? ' show' : '' ?>"><?= htmlspecialchars($message) ?></div>
    <div id="lightbox" class="lightbox">
        <button class="lb-close" id="lbClose">✕</button>
        <button class="lb-btn lb-prev" id="lbPrev">‹</button>
        <img id="lbImg" src="" alt="">
        <button class="lb-btn lb-next" id="lbNext">›</button>
    </div>
    <script>
    (function(){
        const dz = document.getElementById('dropzone');
        const fi = document.getElementById('fileInput');
        const pv = document.getElementById('preview');
        function showPreview(file){
            if (!file) return; const r = new FileReader();
            r.onload = function(){ pv.style.display='block'; pv.innerHTML = '<img src="'+r.result+'" style="max-width:240px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.1)" />'; };
            r.readAsDataURL(file);
        }
        dz.addEventListener('click', function(){ fi.click(); });
        dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('dragover'); });
        dz.addEventListener('dragleave', function(e){ dz.classList.remove('dragover'); });
        dz.addEventListener('drop', function(e){ e.preventDefault(); dz.classList.remove('dragover'); if (e.dataTransfer.files && e.dataTransfer.files[0]) { fi.files = e.dataTransfer.files; showPreview(fi.files[0]); }});
        fi.addEventListener('change', function(){ if (fi.files[0]) showPreview(fi.files[0]); });

        // State & helpers
        const grid = document.getElementById('annGrid');
        const search = document.getElementById('searchAnnouncements');
        const sortSel = document.getElementById('sortSelect');
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        const perPage = document.getElementById('perPage');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');
        const bulkForm = document.getElementById('bulkForm');
        const bulkIds = document.getElementById('bulkIds');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        let page = 1;

        function applyFilters() {
            const q = (search.value || '').trim().toLowerCase();
            const from = dateFrom.value || '';
            const to = dateTo.value || '';
            const cards = Array.from(grid.querySelectorAll('.ann-card'));
            cards.forEach(c => c.style.display = '');
            const filtered = cards.filter(card => {
                const t = card.getAttribute('data-title') || '';
                const d = card.getAttribute('data-date') || '';
                if (q && t.indexOf(q) === -1) return false;
                if (from && d < from) return false;
                if (to && d > to) return false;
                return true;
            });
            // Sort
            const mode = sortSel.value;
            filtered.sort((a,b)=>{
                const ta = a.getAttribute('data-title')||''; const tb = b.getAttribute('data-title')||'';
                const da = a.getAttribute('data-date')||''; const db = b.getAttribute('data-date')||'';
                if (mode==='title_asc') return ta.localeCompare(tb);
                if (mode==='title_desc') return tb.localeCompare(ta);
                if (mode==='date_asc') return da.localeCompare(db);
                return db.localeCompare(da);
            });
            // Paginate render
            const pp = parseInt(perPage.value,10) || 12;
            const totalPages = Math.max(1, Math.ceil(filtered.length / pp));
            if (page > totalPages) page = totalPages;
            const start = (page-1)*pp;
            const end = start + pp;
            cards.forEach(c=>c.remove());
            filtered.forEach((c,i)=>{ if (i>=start && i<end) grid.appendChild(c); });
            pageInfo.textContent = `Page ${page} / ${totalPages}`;
            prevBtn.disabled = (page<=1);
            nextBtn.disabled = (page>=totalPages);
        }

        [search, sortSel, dateFrom, dateTo, perPage].forEach(ctrl=> ctrl && ctrl.addEventListener('input', ()=>{ page=1; applyFilters(); }));
        prevBtn && prevBtn.addEventListener('click', function(){ page=Math.max(1,page-1); applyFilters(); });
        nextBtn && nextBtn.addEventListener('click', function(){ page=page+1; applyFilters(); });
        applyFilters();

        // Bulk selection
        function refreshBulk(){
            const sel = grid.querySelectorAll('.ann-card .sel:checked');
            bulkIds.innerHTML = '';
            sel.forEach(chk=>{
                const card = chk.closest('.ann-card');
                const id = card && card.getAttribute('data-id');
                if (id) {
                    const h = document.createElement('input');
                    h.type='hidden'; h.name='ids[]'; h.value=id; bulkIds.appendChild(h);
                }
            });
            bulkDeleteBtn.disabled = (bulkIds.children.length===0);
        }
        grid.addEventListener('change', function(e){ if (e.target.classList.contains('sel')) refreshBulk(); });

        // Lightbox
        const lb = document.getElementById('lightbox');
        const lbImg = document.getElementById('lbImg');
        const lbPrev = document.getElementById('lbPrev');
        const lbNext = document.getElementById('lbNext');
        const lbClose = document.getElementById('lbClose');
        let lbItems = []; let lbIndex = 0;
        function openLb(index){ lbIndex=index; lbImg.src = lbItems[lbIndex].src; lb.classList.add('open'); }
        function closeLb(){ lb.classList.remove('open'); }
        function prevLb(){ lbIndex = (lbIndex-1+lbItems.length)%lbItems.length; lbImg.src = lbItems[lbIndex].src; }
        function nextLb(){ lbIndex = (lbIndex+1)%lbItems.length; lbImg.src = lbItems[lbIndex].src; }
        grid.addEventListener('click', function(e){
            const img = e.target.closest('img.lb'); if (!img) return;
            lbItems = Array.from(grid.querySelectorAll('img.lb'));
            const idx = lbItems.indexOf(img); if (idx>=0) openLb(idx);
        });
        lbPrev.addEventListener('click', prevLb);
        lbNext.addEventListener('click', nextLb);
        lbClose.addEventListener('click', closeLb);
        document.addEventListener('keydown', function(e){ if (!lb.classList.contains('open')) return; if (e.key==='Escape') closeLb(); if (e.key==='ArrowLeft') prevLb(); if (e.key==='ArrowRight') nextLb(); });

        // Toast auto-hide
        const toast = document.getElementById('toast');
        if (toast && toast.textContent.trim() !== '') {
            setTimeout(function(){ toast.classList.remove('show'); }, 2500);
        }
    })();
    </script>
</body>
</html>