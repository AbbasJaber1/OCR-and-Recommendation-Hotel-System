<?php
session_start();
include 'connect.php';

$search = '';

// If Admin, show all guests. Otherwise, show only logged-in user.

$sql = "SELECT guest_name FROM guests";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['search']))) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT guest_name FROM guests 
            WHERE (guest_name LIKE '%$search%')";
    
        
    
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out</title>

    <link rel="stylesheet" href="dailyreport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" crossorigin="anonymous">
        

    <script src="jquery-3.7.1.min.js?time=<?php echo time();?>"></script>

    <!-- Load Face API Before script.js -->
    <script defer src="face-recognition-javascript-webcam-faceapi-main/face-api.min.js?time=<?php echo time();?>"></script>
    <script defer src="face-recognition-javascript-webcam-faceapi-main/script.js?time=<?php echo time();?>"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js?time=<?php echo time();?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js?time=<?php echo time();?>"></script>
  
   

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-success navbar-light">
        <div class="container-xxl">
            <a class="navbar-brand" href="#">
            <img src="assets/logo/Full_logo.png" alt="" width="auto" height="30" center>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-end pe-3" id="main-nav">
        <ul class="navbar-nav">

        <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئسية</a></li> 
        
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

    <!-- Page Content -->
    <div class="section" id="body">
        <!-- Check-Out Section -->
        <div class="container-md mt-4 border border-4 border-success">
            <div class="row mb-2">
                <div class="col-md-12 bg-success">
                    <h1 class="text-center p-1 fs-1 text-white">تسجيل دخول</h1>
                </div>
            </div>

            <form method="POST" class="mb-3 mt-3 row">
                <button type="submit" class="col-1 btn btn-primary rounded-pill border border-5 ms-5 me-2">بحث</button>
                <input class="col-10 rounded-pill border border-5 ms-3" 
                       type="text" name="search" placeholder="ابحث عن اسم " 
                       value="<?php echo htmlspecialchars($search); ?>" />
            </form>
            

            <div class="row text-center m-1">
                <div class="col-3 bg-success text-white p-1"><h5>تسجل</h5></div>
                <div class="col-3 bg-success text-white p-1"><h5> </h5></div>
                <div class="col-3 bg-success text-white p-1"><h5> </h5></div>
                <div class="col-3 bg-success text-white p-1"><h5>الاسم</h5></div>
            </div>
            
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="row m-1">
                            <div class="col-3 rounded-pill border border-5 bg-danger text-center">
                                <button type="button" class="btn text-white fw-bold" 
                                onclick="startFaceRecognition('<?php echo $row['guest_name']; ?>', this)">
                                    ارسل
                                </button>
                            </div>
                            
                            <div class="col-9 rounded-pill border border-5 bg-primary text-white p-1">
                                <h5 class="text-center"><?php echo $row['guest_name']; ?></h5>
                            </div>
                        </div>
            <?php endwhile; ?>
        </div>

        <!-- Check-In Table (Visible & Updates Automatically) -->
        <div id="CheckInAjax">
            <?php include 'CheckInTable.php'; ?>
        </div>
    </div>

    <script>

        function CheckInS() {
        $.ajax({
            url: 'CheckInTable.php', // PHP script for updated data
            type: 'POST',           // Request type
            async: true,            // Asynchronous request
            data: {
                show: 1             
            },
            success: function(response) {
                // Replace the content of #CheckInAjax
                $('#CheckInAjax').html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }

        function checkIn(logId) {
        const xhr = new XMLHttpRequest();
            xhr.open("POST", "checkin_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = xhr.responseText.trim();

                    // Check for success message
                    if (response === "Check-in successful") {
                        // Refresh the CheckIn table dynamically
                        CheckInS();
                        console.log("Check-in successful");
                    } else {
                        console.error("Error during check-in:", response);
                    }
                } else {
                    console.error("Error during check-in:", xhr.statusText);
                }
            };

            xhr.onerror = function () {
                console.error("Request failed.");
            };

            xhr.send(`logId=${logId}`);
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js?time=<?php echo time();?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js?time=<?php echo time();?>"></script>
</body>
</html>

<?php $conn->close(); ?>
