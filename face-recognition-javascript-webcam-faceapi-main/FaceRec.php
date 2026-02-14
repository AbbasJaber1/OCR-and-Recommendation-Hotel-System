<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Face detection</title>
  <script defer src="face-api.min.js"></script>
  <script defer src="script.js"></script>
  <!-- Bootstrap 5 CSS (Already Included) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<!-- Bootstrap 5 JavaScript Bundle (Include this) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
crossorigin="anonymous"></script>

<link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success navbar-light">
<div class="container-xxl">
<a class="navbar-brand" href="#">
    <img src="../assets/sfr5kzys.png" alt="" width="auto" height="30">
</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
        aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
</div>
<div class="collapse navbar-collapse justify-content-end pe-3" id="main-nav">
        <ul class="navbar-nav">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <!-- Admin-specific nav items -->
            <li class="nav-item"><a class="nav-link text-white me-2" href="../Edit.php">تعديل</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="../CheckIn.php">تسجيل دخول</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="../DailyReport.php">الكشف اليومي</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="../RegesterNew.php">تسجيل الجدد</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link text-white" href="../CheckOut.php">تسجيل خروج</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="../index.php">logout </a></li>   
        
    </ul>
        </div>
</nav>

<!-- Video Container (Centered) -->
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="position-relative">
    <video id="video" width="600" height="450" autoplay></video>
    <canvas id="overlay"></canvas> <!-- Canvas for face detection -->
  </div>
</div>

</body>
</html>
