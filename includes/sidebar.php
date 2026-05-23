<?php $cp = basename($_SERVER['PHP_SELF']); ?>
<div class="hs-sidebar-overlay" id="sidebarOverlay"></div>
<aside class="hs-sidebar" id="sidebar">
  <div class="hs-sidebar-hd">
    <div class="hs-sidebar-logo" style="background:none;box-shadow:none;padding:2px">
      <img src="assets/logo/logo.png" alt="Logo" style="width:34px;height:34px;object-fit:contain;filter:brightness(0) invert(1)">
    </div>
    <div class="hs-sidebar-brand">
      <div class="hs-sidebar-brand-nm">الفندق</div>
      <div class="hs-sidebar-brand-sub">نظام إدارة فندقي</div>
    </div>
    <button class="hs-sidebar-toggle" id="sidebarToggle" title="طي القائمة">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <nav class="hs-sidebar-nav">
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='index.php'?'active':'' ?>" href="index.php">
        <span class="hs-nav-ic"><i class="fas fa-home"></i></span>
        <span class="hs-nav-tx" data-i18n="home">الرئيسية</span>
      </a>
    </div>

    <div class="hs-nav-sec" data-i18n="operations">العمليات</div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='RegesterNewGuest.php'?'active':'' ?>" href="RegesterNewGuest.php">
        <span class="hs-nav-ic"><i class="fas fa-passport"></i></span>
        <span class="hs-nav-tx" data-i18n="guest_reg">تسجيل ضيف جديد</span>
      </a>
    </div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='CheckOut.php'?'active':'' ?>" href="CheckOut.php">
        <span class="hs-nav-ic"><i class="fas fa-sign-out-alt"></i></span>
        <span class="hs-nav-tx" data-i18n="checkout">تسجيل المغادرة</span>
      </a>
    </div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='CheckIn.php'?'active':'' ?>" href="CheckIn.php">
        <span class="hs-nav-ic"><i class="fas fa-sign-in-alt"></i></span>
        <span class="hs-nav-tx" data-i18n="checkin">تسجيل الوصول</span>
      </a>
    </div>

    <div class="hs-nav-sec" data-i18n="management">الإدارة</div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='RegesterNew.php'?'active':'' ?>" href="RegesterNew.php">
        <span class="hs-nav-ic"><i class="fas fa-user-plus"></i></span>
        <span class="hs-nav-tx" data-i18n="staff_reg">تسجيل موظف</span>
      </a>
    </div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='Edit.php'?'active':'' ?>" href="Edit.php">
        <span class="hs-nav-ic"><i class="fas fa-edit"></i></span>
        <span class="hs-nav-tx" data-i18n="rooms_guests">إدارة الغرف والضيوف</span>
      </a>
    </div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='DailyReport.php'?'active':'' ?>" href="DailyReport.php">
        <span class="hs-nav-ic"><i class="fas fa-chart-bar"></i></span>
        <span class="hs-nav-tx" data-i18n="daily_report">التقرير اليومي</span>
      </a>
    </div>

    <div class="hs-nav-sec" data-i18n="recommendations">التوصيات</div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='RecommendationService.php'?'active':'' ?>" href="RecommendationService.php">
        <span class="hs-nav-ic"><i class="fas fa-map-marked-alt"></i></span>
        <span class="hs-nav-tx" data-i18n="rec_service">خدمة التوصيات</span>
      </a>
    </div>
    <div class="hs-nav-item">
      <a class="hs-nav-link <?= $cp==='RecommendationSettings.php'?'active':'' ?>" href="RecommendationSettings.php">
        <span class="hs-nav-ic"><i class="fas fa-cog"></i></span>
        <span class="hs-nav-tx" data-i18n="rec_settings">إعدادات التوصيات</span>
      </a>
    </div>
  </nav>

  <div class="hs-sidebar-ft">
    <button class="hs-sidebar-ft-btn" onclick="Lang.toggle()">
      <i class="fas fa-globe" style="font-size:15px"></i>
      <span id="langBtn" class="hs-nav-tx">English</span>
    </button>
  </div>
</aside>
