<?php

include 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In</title>
    <link rel="stylesheet" href="dailyreport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="jquery-3.7.1.min.js" defer></script>
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
            <li class="nav-item"><a class="nav-link text-white me-2" href="Edit.php">تعديل</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckIn.php">تسجيل دخول</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckOut.php">تسجيل خروج</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="DailyReport.php">الكشف اليومي</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="RegesterNew.php">تسجيل الجدد</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئسية</a></li>  
        </ul>
    </div>
</nav>

<!--codes added newly -->

        <div id = "CheckInAjax">
        <!-- Check-in table -->
         <?php include 'CheckInTable.php'; 
            ?>
        
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
    
    </script>
</body>
</html>

<?php
$conn->close();
?>
