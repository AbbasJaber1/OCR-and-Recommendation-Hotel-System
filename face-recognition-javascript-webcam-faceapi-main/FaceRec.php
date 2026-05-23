<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>التعرف على الوجه — الفندق</title>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Bootstrap RTL -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Face Recognition Scripts & Original Styles -->
  <link rel="stylesheet" href="style.css">
  <script defer src="face-api.min.js"></script>
  <script defer src="script.js"></script>

  <style>
    :root {
      --g-950:#052e0a; --g-900:#0a3d11; --g-800:#145220;
      --g-700:#1B5E20; --g-600:#2E7D32; --g-500:#388E3C;
      --g-400:#43A047; --g-300:#66BB6A;
      --au-500:#C9A84C; --au-300:#E8C84A; --au-200:#F0D080;
      --tx-1:#1A2C1A; --tx-3:#7A8E7A;
      --bd-1:#DDE8DD;
      --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
      --bar-h:64px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Cairo', 'Inter', sans-serif;
      background: var(--g-950);
      min-height: 100vh;
      color: #fff;
      direction: rtl;
    }

    /* Topbar */
    .fr-topbar {
      height: var(--bar-h);
      background: rgba(5,46,10,.85);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(255,255,255,.07);
      display: flex;
      align-items: center;
      padding: 0 24px;
      gap: 14px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .fr-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
    }
    .fr-brand-ic {
      width: 36px; height: 36px;
      border-radius: var(--r-md);
      background: linear-gradient(135deg,var(--au-300),var(--au-500));
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: var(--tx-1);
    }
    .fr-brand-name {
      font-size: .95rem; font-weight: 700; color: #fff;
    }
    .fr-nav {
      display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    }
    .fr-nav-link {
      padding: 6px 14px;
      border-radius: var(--r-full);
      color: rgba(255,255,255,.6);
      font-size: .82rem;
      font-weight: 500;
      text-decoration: none;
      transition: all .15s ease;
      border: 1px solid transparent;
    }
    .fr-nav-link:hover {
      background: rgba(255,255,255,.08);
      color: #fff;
      border-color: rgba(255,255,255,.1);
    }
    .fr-nav-link.active {
      background: rgba(67,160,71,.18);
      color: var(--g-300);
      border-color: rgba(67,160,71,.25);
    }

    /* Video container */
    .fr-scene {
      min-height: calc(100vh - var(--bar-h));
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
      background: radial-gradient(ellipse at center, rgba(20,82,32,.4) 0%, transparent 70%);
    }
    .fr-box {
      position: relative;
      border-radius: var(--r-xl);
      overflow: hidden;
      border: 2px solid var(--g-700);
      box-shadow: 0 0 60px rgba(46,125,50,.3), 0 0 0 1px rgba(201,168,76,.08);
    }
    .fr-box video {
      display: block;
      max-width: 100%;
    }
    .fr-box canvas {
      position: absolute;
      top: 0; left: 0;
    }

    /* Corner accents */
    .fr-corner {
      position: absolute;
      width: 22px; height: 22px;
      border-color: var(--au-300);
      border-style: solid;
      pointer-events: none;
      z-index: 2;
    }
    .fr-c-tl { top: 10px; right: 10px; border-width: 2px 0 0 2px; border-radius: 0 0 4px 0; }
    .fr-c-tr { top: 10px; left: 10px;  border-width: 2px 2px 0 0; border-radius: 0 0 0 4px; }
    .fr-c-bl { bottom: 10px; right: 10px; border-width: 0 0 2px 2px; border-radius: 4px 0 0 0; }
    .fr-c-br { bottom: 10px; left: 10px;  border-width: 0 2px 2px 0; border-radius: 0 4px 0 0; }

    /* Status label */
    .fr-status {
      position: absolute;
      bottom: 14px;
      left: 50%; transform: translateX(-50%);
      background: rgba(5,46,10,.8);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(67,160,71,.3);
      border-radius: var(--r-full);
      padding: 6px 18px;
      font-size: .8rem;
      color: var(--g-300);
      white-space: nowrap;
      z-index: 2;
      display: flex; align-items: center; gap: 8px;
    }
    .fr-pulse {
      width: 7px; height: 7px;
      background: var(--g-400);
      border-radius: 50%;
      animation: pulse 1.8s ease-in-out infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }
  </style>
</head>
<body>

<!-- Topbar -->
<header class="fr-topbar">
  <div class="fr-brand">
    <div class="fr-brand-ic"><i class="fas fa-hotel"></i></div>
    <span class="fr-brand-name">نظام الفندق</span>
  </div>
  <nav class="fr-nav">
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <a class="fr-nav-link" href="../Edit.php"><i class="fas fa-edit me-1"></i> تعديل</a>
      <a class="fr-nav-link" href="../CheckIn.php"><i class="fas fa-sign-in-alt me-1"></i> تسجيل دخول</a>
      <a class="fr-nav-link" href="../DailyReport.php"><i class="fas fa-chart-bar me-1"></i> الكشف اليومي</a>
      <a class="fr-nav-link" href="../RegesterNew.php"><i class="fas fa-user-plus me-1"></i> تسجيل الجدد</a>
    <?php endif; ?>
    <a class="fr-nav-link" href="../CheckOut.php"><i class="fas fa-sign-out-alt me-1"></i> تسجيل خروج</a>
    <a class="fr-nav-link" href="../index.php"><i class="fas fa-power-off me-1"></i> خروج</a>
  </nav>
</header>

<!-- Scene -->
<div class="fr-scene">
  <div class="fr-box">
    <video id="video" width="600" height="450" autoplay></video>
    <canvas id="overlay"></canvas>
    <div class="fr-corner fr-c-tl"></div>
    <div class="fr-corner fr-c-tr"></div>
    <div class="fr-corner fr-c-bl"></div>
    <div class="fr-corner fr-c-br"></div>
    <div class="fr-status">
      <span class="fr-pulse"></span>
      جاري التعرف على الوجه...
    </div>
  </div>
</div>

</body>
</html>
