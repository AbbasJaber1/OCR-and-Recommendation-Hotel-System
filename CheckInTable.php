<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php';

// Fetch updated guest logs
$guest_logs = [];
$guest_query = $conn->prepare("SELECT cl.log_id, cl.guest_id, g.guest_name 
    FROM check_logs cl
    JOIN guests g ON cl.guest_id = g.guest_id
    WHERE cl.return_time IS NULL");
$guest_query->execute();
$guest_result = $guest_query->get_result();
$guest_logs = $guest_result->fetch_all(MYSQLI_ASSOC);
$arr_of_guest = [];

foreach ($guest_logs as $log) {
    // Check if the guest_id matches the user_id from the session
    
        // Add the matching guest row to the $arr_of_guest array
        $arr_of_guest[] = $log;
    
}

// Search functionality
$search = '';
$search_results = $guest_logs;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $search = $conn->real_escape_string(trim($_POST['search']));
    $search_query = "SELECT cl.log_id, cl.guest_id, g.guest_name
                     FROM check_logs cl 
                     JOIN guests g ON cl.guest_id = g.guest_id 
                     WHERE cl.return_time IS NULL 
                     AND (g.guest_name LIKE '%$search%')";
    $search_result = $conn->query($search_query);
    if ($search_result) {
        $search_results = $search_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $search_results = [];
    }
}

// Generate HTML output for the table
?>
<div class="container-md mt-4 border border-4 border-success">
    <div class="row mb-2">
        <div class="col-md-12 bg-success">
            <h1 class="text-center p-1 fs-1 text-white">تسجيل دخول</h1>
        </div>
    </div>
    
   
        <!-- Search Bar -->
        <form method="POST" class="mb-3 mt-3 row">
            <button type="submit" class="col-1 btn btn-primary rounded-pill border border-5 ms-5 me-2">بحث</button>
            <input class="col-10 rounded-pill border border-5 ms-3" 
                   type="text" name="search" placeholder="ابحث عن اسم أو رقم غرفة" 
                   value="<?php echo htmlspecialchars($search); ?>" />
        </form>
 

    <!-- Column Titles -->
    <div class="row m-2 text-center">
        <div class="col-3 rounded-pill border border-5 bg-success text-white p-1">
            <h5 class="text-center">تسجيل</h5>
        </div>
        <div class="col-3 rounded-pill border border-5 bg-success text-white p-1">
            <h5 class="text-center">سبب الخروج</h5>
        </div>
        <div class="col-3 rounded-pill border border-5 bg-success text-white p-1">
            <h5 class="text-center">رقم الغرفة</h5>
        </div>
        <div class="col-3 rounded-pill border border-5 bg-success text-white p-1">
            <h5 class="text-center">الاسم</h5>
        </div>
    </div>

    <!-- Data Rows -->

    
        <?php foreach ($arr_of_guest as $log): ?>
            <div class="row m-1">
                <div class="col-3 rounded-pill border border-5 bg-danger text-center">
                    <button type="button" class="btn text-white fw-bold" 
                            onclick="checkIn(<?php echo $log['log_id']; ?>)">
                        ارسل
                    </button>
                </div>
            
                <div class="col-9 rounded-pill border border-5 bg-primary text-white p-1">
                    <h5 class="text-center"><?php echo htmlspecialchars($log['guest_name']); ?></h5>
                </div>
            </div>
        <?php endforeach;
            if(empty($arr_of_guest)){?>
                <div class="row m-1">
            <div class="col-12 text-center">
                <h5 class="text-danger">لا توجد بيانات للعرض</h5>
            </div>
        </div>
        <?php
            }
        ?>
</div>