<?php
session_start();
require "connect.php";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الفندق — نظام الإدارة</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/hotel-system.css">
  <script src="assets/js/translations.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="face-recognition-javascript-webcam-faceapi-main/face-api.min.js?t=<?= time() ?>"></script>
  <script defer src="face-recognition-javascript-webcam-faceapi-main/script.js?t=<?= time() ?>"></script>
  <script defer src="FaceRecForRoles.js?t=<?= time() ?>"></script>
  <style>
    body { background: #052e0a }
    .land-nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 200;
      padding: 18px 32px;
      display: flex; align-items: center; justify-content: space-between;
      background: linear-gradient(180deg, rgba(5,46,10,.9) 0%, transparent 100%);
    }
    .land-nav-logo { display:flex; align-items:center; gap:12px }
    .land-nav-logo-ic {
      display:flex; align-items:center;
    }
    .land-nav-logo-tx { font-size:.95rem; font-weight:700; color:#fff }
    .land-nav-logo-sub { font-size:.72rem; color:rgba(255,255,255,.45) }
  </style>
</head>
<body>

<!-- Top Bar -->
<div class="land-nav">
  <div class="land-nav-logo">
    <div class="land-nav-logo-ic"><img src="assets/logo/Full_logo.png" alt="Logo" style="height:36px;width:auto"></div>
    <div>
      <div class="land-nav-logo-tx">الفندق</div>
      <div class="land-nav-logo-sub">نظام إدارة فندقي متكامل</div>
    </div>
  </div>
  <button class="hs-lang-btn" onclick="Lang.toggle()" style="border-color:rgba(255,255,255,.3);background:rgba(255,255,255,.08);color:#fff">
    <i class="fas fa-globe" style="font-size:13px"></i>
    <span id="langBtn">English</span>
  </button>
</div>

<!-- Face Recognition Modal (preserved exactly) -->
<div class="modal fade" id="faceRecModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:1px solid rgba(255,255,255,.1);background:#0a1a0a;overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,#145220,#1B5E20);border-bottom:1px solid rgba(255,255,255,.08)">
        <h5 class="modal-title text-white" style="font-size:.95rem;font-weight:700">
          <i class="fas fa-camera me-2" style="color:#E8C84A"></i>
          تأكيد الهوية باستخدام التعرف على الوجه
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopWebcam()"></button>
      </div>
      <div class="modal-body text-center p-4" style="background:#0D1B0D">
        <div style="position:relative;border-radius:16px;overflow:hidden;background:#000;margin-bottom:16px">
          <video id="faceRecVideo" width="100%" autoplay style="display:block"></video>
          <canvas id="faceRecOverlay" style="position:absolute;top:0;left:0"></canvas>
          <div style="position:absolute;inset:16px;border:2px solid #43A047;border-radius:12px;pointer-events:none;animation:ocrPulse 2s ease-in-out infinite"></div>
        </div>
        <div style="display:flex;align-items:center;justify-content:center;gap:10px;background:rgba(255,255,255,.04);border-radius:12px;padding:12px;border:1px solid rgba(255,255,255,.06)">
          <div class="hs-spin" style="border-top-color:#43A047;width:16px;height:16px;border-width:2px"></div>
          <p id="faceRecStatus" class="mb-0" style="color:rgba(255,255,255,.7);font-size:.875rem">يرجى النظر إلى الكاميرا...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Landing Page -->
<div class="hs-landing" style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;padding:80px 24px 40px">
  <!-- Background glows -->
  <div class="hs-landing-glow hs-landing-glow-1"></div>
  <div class="hs-landing-glow hs-landing-glow-2"></div>

  <!-- Geometric pattern -->
  <div class="hs-geo">
    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" style="top:0;left:0">
      <defs>
        <pattern id="geo" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
          <polygon points="40,0 80,20 80,60 40,80 0,60 0,20" fill="none" stroke="#fff" stroke-width=".6"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#geo)" opacity=".04"/>
    </svg>
  </div>

  <!-- Hero Text -->
  <div style="text-align:center;margin-bottom:56px;position:relative;z-index:1" class="hs-fade">
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);border-radius:999px;padding:6px 16px;margin-bottom:20px">
      <span style="color:#E8C84A;font-size:.8rem;font-weight:600" data-i18n="land_sub">منصة متكاملة للإدارة الفندقية الذكية</span>
    </div>
    <h1 style="font-size:clamp(2rem,5vw,3.5rem);font-weight:900;color:#fff;margin-bottom:14px;line-height:1.2" data-i18n="land_title">نظام إدارة الفندق</h1>
    <p style="color:rgba(255,255,255,.5);font-size:1rem;max-width:480px;margin:0 auto" data-i18n="land_sub">منصة متكاملة للإدارة الفندقية الذكية</p>
  </div>

  <!-- Role Cards -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;max-width:860px;width:100%;position:relative;z-index:1" class="hs-stagger">

    <!-- Admin -->
    <div class="hs-role-card" onclick="startFaceRecognition1()" style="cursor:pointer">
      <div class="hs-role-ic">🛡️</div>
      <h3 class="hs-role-title" data-i18n="land_admin">الإدارة</h3>
      <p class="hs-role-desc" data-i18n="land_admin_d">لوحة التحكم الإدارية وإدارة الموظفين</p>
      <div style="margin-top:20px">
        <span style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.4);font-size:.8rem">
          <i class="fas fa-fingerprint"></i> تسجيل بالوجه
        </span>
      </div>
    </div>

    <!-- Check-In (direct link) -->
    <a class="hs-role-card" href="CheckOut.php" style="display:block;text-decoration:none">
      <div class="hs-role-ic">🔑</div>
      <h3 class="hs-role-title" data-i18n="land_checkin">تسجيل الوصول</h3>
      <p class="hs-role-desc" data-i18n="land_checkin_d">تسجيل وصول الضيوف وتتبع الغرف</p>
      <div style="margin-top:20px">
        <span style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.4);font-size:.8rem">
          <i class="fas fa-arrow-left"></i> دخول مباشر
        </span>
      </div>
    </a>

    <!-- Reception -->
    <div class="hs-role-card" onclick="startFaceRecognition3()" style="cursor:pointer">
      <div class="hs-role-ic">🛎️</div>
      <h3 class="hs-role-title" data-i18n="land_reception">الاستقبال</h3>
      <p class="hs-role-desc" data-i18n="land_reception_d">استقبال الضيوف الجدد وإدارة الحجوزات</p>
      <div style="margin-top:20px">
        <span style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.4);font-size:.8rem">
          <i class="fas fa-fingerprint"></i> تسجيل بالوجه
        </span>
      </div>
    </div>

  </div>

  <!-- Footer -->
  <p style="color:rgba(255,255,255,.2);font-size:.78rem;margin-top:48px;position:relative;z-index:1">
    نظام إدارة الفندق &copy; <?= date('Y') ?>
  </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', ()=>{ Lang.init(); });
</script>
</body>
</html>
