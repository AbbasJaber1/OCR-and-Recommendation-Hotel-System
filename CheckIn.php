<?php
include 'connect.php';
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
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title" data-i18n="ci_title">تسجيل الوصول</div>
          <div class="hs-pg-sub" data-i18n="ci_sub">الضيوف في انتظار الوصول</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn" onclick="CheckInS()" title="تحديث"><i class="fas fa-sync-alt"></i></button>
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill"><div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div><span class="hs-uname">موظف</span></div>
      </div>
    </header>

    <main class="hs-content">
      <div id="CheckInAjax">
        <?php include 'CheckInTable.php'; ?>
      </div>
    </main>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function CheckInS() {
  $.ajax({ url:'CheckInTable.php', type:'POST', data:{show:1},
    success: r => { $('#CheckInAjax').html(r); HsToast.success('تم التحديث'); },
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
      HsToast.success('تم تسجيل الوصول بنجاح');
    }
  };
  xhr.send(`logId=${logId}`);
}
</script>
</body>
</html>
<?php $conn->close(); ?>
