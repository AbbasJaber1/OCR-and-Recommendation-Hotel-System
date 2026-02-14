<?php
session_start();
require "connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Face API -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?time=<?php echo time();?>"></script>
    <script defer src="face-recognition-javascript-webcam-faceapi-main/face-api.min.js?time=<?php echo time();?>"></script>
    <script defer src="face-recognition-javascript-webcam-faceapi-main/script.js?time=<?php echo time();?>"></script>
    <script defer src="FaceRecForRoles.js?time=<?php echo time();?>"></script>

    <script>

    </script>
    
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success navbar-light">
    <div class="container-xxl">
        <a class="navbar-brand" href="#">
            <img src="assets/logo/Full_logo.png" alt="" width="auto" height="30">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse justify-content-end pe-3" id="main-nav">
        <ul class="navbar-nav"> 

        </ul>
    </div>
</nav>

<!-- Face Recognition Modal -->
<div class="modal fade" id="faceRecModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الهوية باستخدام التعرف على الوجه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <video id="faceRecVideo" width="100%" height="100%" autoplay></video>
                <canvas id="faceRecOverlay"></canvas>
                <p id="faceRecStatus" class="mt-3 text-danger">يرجى النظر إلى الكاميرا...</p>
            </div>
        </div>
    </div>
</div>

<!-- Background Image -->
<div class="position-relative min-vh-100">
    <img src="assets/pictuers/banner_3.jpg" class="img-fluid position-absolute w-100 h-100" alt="Hotel Reception" style="object-fit: cover; z-index: -1;">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row row-cols-1 row-cols-md-3 g-4 w-75 text-center">
            <div class="col">
                <div class="card h-100">
                    <img src="assets/pictuers/2.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="startFaceRecognition1()">
                            <h5 class="card-title">الادارة</h5>
                        </button>
                    </div>
                </div>
            </div>

         
            <div class="col">
                <div class="card h-100">
                    <img src="assets/pictuers/3.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <a class="btn btn-danger" href="CheckOut.php"><h5 class="card-title">تسجيل الدخول</h5></a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="assets/pictuers/4.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                    <button class="btn btn-warning" onclick="startFaceRecognition3()">
                        <h5 class="card-title">الاستقبال</h5>
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js?time=<?php echo time();?>"></script>
</body>
</html>
