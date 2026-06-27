<?php
session_start();
include 'connect.php';

$search = '';
$sql = "SELECT guest_name FROM guests";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['search']))) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT guest_name FROM guests WHERE guest_name LIKE '%$search%'";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <title>تسجيل الوصول — الفندق</title>
  <?php include 'includes/head.php'; ?>
</head>
<body>
<div class="hs-app">
  <?php include 'includes/sidebar.php'; ?>

  <div class="hs-main" id="mainContent">
    <!-- Topbar -->
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title" data-i18n="ci_title">تسجيل الوصول</div>
          <div class="hs-pg-sub" data-i18n="ci_sub">تسجيل وصول الموظفين والضيوف</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill"><div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div><span class="hs-uname" data-i18n="employee">موظف</span></div>
      </div>
    </header>

    <!-- Content -->
    <main class="hs-content hs-stagger">

      <!-- Search Card -->
      <div class="hs-card hs-mb-6">
        <div class="hs-card-hd">
          <div class="hs-card-title">
            <div class="hs-card-ic"><i class="fas fa-sign-in-alt"></i></div>
            <span data-i18n="ci_title">تسجيل الوصول</span>
          </div>
          <span class="hs-badge hs-badge-g"><i class="fas fa-users me-1"></i><?= $result->num_rows ?> <span data-i18n="employee">موظف</span></span>
        </div>
        <div class="hs-card-bd">
          <form method="POST">
            <div class="hs-search-bar" style="max-width:520px">
              <i class="hs-search-ic fas fa-search"></i>
              <input class="hs-search-in" type="text" name="search"
                     data-i18n="search_employee_ph" data-i18n-attr="placeholder"
                     placeholder="ابحث عن اسم الموظف..."
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <div style="display:flex;gap:10px;margin-top:14px">
              <button type="submit" class="hs-btn hs-btn-primary hs-btn-sm">
                <i class="fas fa-search"></i> <span data-i18n="search">بحث</span>
              </button>
              <?php if($search): ?>
              <a href="CheckOut.php" class="hs-btn hs-btn-ghost hs-btn-sm">
                <i class="fas fa-times"></i> <span data-i18n="clear">مسح</span>
              </a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <!-- Guest List -->
      <div class="hs-card">
        <div class="hs-card-hd">
          <div class="hs-card-title">
            <div class="hs-card-ic" style="background:#FEF2F2;color:#DC2626"><i class="fas fa-list"></i></div>
            <span data-i18n="employees_list">قائمة الموظفين</span>
          </div>
        </div>
        <div class="hs-card-bd" style="padding:0">
          <?php if($result->num_rows === 0): ?>
            <div style="text-align:center;padding:48px 24px;color:var(--tx-3)">
              <i class="fas fa-users" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3"></i>
              <p data-i18n="no_match">لا توجد نتائج مطابقة</p>
            </div>
          <?php else: ?>
            <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
              <?php while($row = $result->fetch_assoc()): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;background:var(--s-1);border-radius:var(--r-lg);border:1px solid var(--bd-1);transition:var(--tf)" onmouseover="this.style.borderColor='var(--bd-2)';this.style.background='var(--g-25)'" onmouseout="this.style.borderColor='var(--bd-1)';this.style.background='var(--s-1)'">
                  <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--g-500),var(--g-700));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0">
                      <?= mb_substr($row['guest_name'], 0, 1, 'UTF-8') ?>
                    </div>
                    <div>
                      <div style="font-weight:600;color:var(--tx-1)"><?= htmlspecialchars($row['guest_name']) ?></div>
                      <div style="font-size:.78rem;color:var(--tx-3)" data-i18n="employee">موظف</div>
                    </div>
                  </div>
                  <button type="button" class="hs-btn hs-btn-primary hs-btn-sm"
                          onclick="startFaceRecognition('<?= htmlspecialchars($row['guest_name'], ENT_QUOTES) ?>', this)">
                    <i class="fas fa-fingerprint"></i> <span data-i18n="ci_title">تسجيل الوصول</span>
                  </button>
                </div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Pending Check-In -->
      <div class="hs-mt-6">
        <div id="CheckInAjax">
          <?php include 'CheckInTable.php'; ?>
        </div>
      </div>

    </main>
  </div><!-- /hs-main -->
</div><!-- /hs-app -->

<!-- Face Recognition Modal (preserved IDs) -->
<div class="modal fade" id="faceRecModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:1px solid var(--bd-1);overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,var(--g-800),var(--g-700))">
        <h5 class="modal-title text-white" style="font-size:.9rem;font-weight:700">
          <i class="fas fa-camera me-2" style="color:var(--au-300)"></i>
          <span data-i18n="face_id_confirm">تأكيد الهوية — التعرف على الوجه</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopWebcam()"></button>
      </div>
      <div class="modal-body text-center p-4" style="background:#0D1B0D">
        <div style="position:relative;border-radius:16px;overflow:hidden;background:#000;margin-bottom:16px">
          <video id="faceRecVideo" width="100%" autoplay style="display:block"></video>
          <canvas id="faceRecOverlay" style="position:absolute;top:0;left:0"></canvas>
          <div style="position:absolute;inset:14px;border:2px solid var(--g-400);border-radius:12px;pointer-events:none;animation:ocrPulse 2s ease-in-out infinite"></div>
        </div>
        <div style="display:flex;align-items:center;justify-content:center;gap:10px;background:rgba(255,255,255,.04);border-radius:12px;padding:10px;border:1px solid rgba(255,255,255,.06)">
          <div class="hs-spin" style="border-top-color:var(--g-400);width:14px;height:14px;border-width:2px"></div>
          <p id="faceRecStatus" class="mb-0" style="color:rgba(255,255,255,.7);font-size:.85rem">يرجى النظر إلى الكاميرا...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="hs-toast-ctr"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="face-recognition-javascript-webcam-faceapi-main/face-api.min.js?t=<?= time() ?>"></script>
<script src="face-recognition-javascript-webcam-faceapi-main/script.js?t=<?= time() ?>"></script>
<script>
function CheckInS() {
  $.ajax({ url:'CheckInTable.php', type:'POST', data:{show:1},
    success: r => $('#CheckInAjax').html(r),
    error: (x,s,e) => console.error('AJAX Error:',s,e)
  });
}
function checkIn(logId) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST','checkin_action.php',true);
  xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  xhr.onload = function() {
    if(xhr.status===200 && xhr.responseText.trim()==='Check-in successful') {
      CheckInS();
      HsToast.success(Lang.t('checkin_success'));
    }
  };
  xhr.send(`logId=${logId}`);
}
</script>
</body>
</html>
<?php $conn->close(); ?>
