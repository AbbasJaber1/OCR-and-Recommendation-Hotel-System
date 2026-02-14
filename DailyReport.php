<?php
include 'connect.php';  // Include database connection

// Set default date to today if not set
$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');

// Handle search input
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Fetch records from check_logs and guests table based on selected date and search input
$sql = "
    SELECT 
        cl.log_id, 
        cl.checkout_time, 
        cl.return_time, 
        g.guest_name 
    FROM 
        check_logs cl 
    JOIN 
        guests g ON cl.guest_id = g.guest_id 
    WHERE 
        DATE(cl.checkout_time) = '$selectedDate' 
        AND (g.guest_name LIKE '%$search%')"; // Filter by selected date and search

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report</title>
    <link rel="stylesheet" href="dailyreport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success navbar-light">
    <div class="container-xxl">
        <a class="navbar-brand" href="#">
        <img src="assets/logo/Full_logo.png" alt="" width="auto" height="30" center>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" 
                aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
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

<!-- Main Section -->
<div class="section" id="body">
    <div class="container-md mt-4 border border-4 border-success">
        <div class="row mb-2">
            <div class="col-md-12 bg-success d-flex align-items-center justify-content-between p-2">
    
                <!-- Calendar SVG Icon -->
                <span class="text-white fs-3" role="button" id="calendarIcon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1zm1-1h12V3a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v1z"/>
                    </svg>
                </span>

                <h1 class="text-center fs-1 text-white flex-grow-1" style="font-family: Arial, Helvetica, sans-serif;">
                    الكشف اليومي
                </h1>

                <input type="date" id="datePicker" value="<?php echo $selectedDate; ?>" style="display: none;">
            </div>
        </div>

        <script>
            const datePicker = document.getElementById('datePicker');
            const calendarIcon = document.getElementById('calendarIcon');

            // Show the date picker when the icon is clicked
            calendarIcon.addEventListener('click', () => {
                datePicker.showPicker();
            });

            // Redirect to the selected date when a date is chosen
            datePicker.addEventListener('change', () => {
                const selectedDate = datePicker.value;
                window.location.href = `DailyReport.php?selected_date=${selectedDate}`;
            });
        </script>

        <!-- Search Form -->
        <form method="POST" class="mb-3 row" action="export_excel.php">
            <button type="button" class="col-1 btn btn-success rounded-pill ms-5 me-2">بحث</button>
            <input class="col-8 rounded-pill border ms-3" type="text" name="search" 
                placeholder="ابحث عن اسم الضيف" value="<?php echo htmlspecialchars($search); ?>" />
                <form method="POST" class="mb-3 row" action="export_excel.php?selected_date=<?php echo $selectedDate; ?>">
                    <button type="submit" class="col-2 btn btn-success rounded-pill ms-2">Excel Report</button>
                </form>
        </form>


        <!-- Header Row -->
        <div class="row m-2 text-center">
            <div class="col-2 bg-success text-white p-1"><h5>الغاء</h5></div>
            <div class="col-2 bg-success text-white p-1"><h5>وقت الخروج</h5></div>
            <div class="col-2 bg-success text-white p-1"><h5>وقت الدخول</h5></div>
            <div class="col-2 bg-success text-white p-1"><h5> </h5></div>
            <div class="col-2 bg-success text-white p-1"><h5></h5></div>
            <div class="col-2 bg-success text-white p-1"><h5>الاسم</h5></div>
            
        </div>

        <!-- Data Rows -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="row m-1 text-center guest-row">
                <div class="col-2 rounded-pill border border-5 bg-danger text-center">
                        <form method="POST" action="delete_log.php" style="display: inline;">
                            <input type="hidden" name="log_id" value="<?php echo $row['log_id']; ?>">
                            <button type="submit" class="btn text-white fw-bold">X</button>
                        </form>
                    </div>
                    <div class="col-2 rounded-pill border border-5 bg-primary text-white">
                        <h5>
                            <?php 
                            echo $row['return_time'] ? date('H:i', strtotime($row['return_time'])) : 'لم يعد بعد'; 
                            ?>
                        </h5>
                    </div>
                    <div class="col-2 rounded-pill border border-5 bg-primary text-white">
                        <h5><?php echo date('H:i', strtotime($row['checkout_time'])); ?></h5>
                    </div>
                    
                    <div class="col-6 rounded-pill border border-5 bg-primary text-white">
                        <h5><?php echo $row['guest_name']; ?></h5>
                    </div>
                    
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="row m-1 text-center">
                <div class="col-12">
                    <h5 class="text-danger">لا توجد بيانات للعرض</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
        crossorigin="anonymous"></script>
</body>
</html>

<?php
$conn->close();  // Close the database connection
?>
