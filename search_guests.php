<?php
include 'connect.php';  // Include database connection

// Retrieve the search term from the AJAX request
$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

$sql = "
    SELECT 
        cl.log_id, 
        cl.checkout_time, 
        cl.return_time, 
        cl.reason_for_leaving, 
        g.room_number, 
        g.guest_name 
    FROM 
        check_logs cl 
    JOIN 
        guests g ON cl.guest_id = g.guest_id 
    WHERE 
        g.guest_name LIKE ?";  // Use parameterized query to prevent SQL injection

$stmt = $conn->prepare($sql);
$searchParam = '%' . $searchTerm . '%';
$stmt->bind_param('s', $searchParam);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML rows with the matching results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='row m-1 text-center'>
            <div class='col-2 rounded-pill border border-5 bg-primary text-white'>
                <h5>" . 
                ($row['return_time'] ? date('H:i', strtotime($row['return_time'])) : 'لم يعد بعد') . 
                "</h5>
            </div>
            <div class='col-2 rounded-pill border border-5 bg-primary text-white'>
                <h5>" . date('H:i', strtotime($row['checkout_time'])) . "</h5>
            </div>
            <div class='col-2 rounded-pill border border-5 bg-primary text-white'>
                <h5>" . htmlspecialchars($row['reason_for_leaving']) . "</h5>
            </div>
            <div class='col-2 rounded-pill border border-5 bg-primary text-white'>
                <h5>" . htmlspecialchars($row['room_number']) . "</h5>
            </div>
            <div class='col-2 rounded-pill border border-5 bg-primary text-white'>
                <h5>" . htmlspecialchars($row['guest_name']) . "</h5>
            </div>
            <div class='col-2 rounded-pill border border-5 bg-danger text-center'>
                <form method='POST' action='delete_log.php' style='display: inline;'>
                    <input type='hidden' name='log_id' value='" . $row['log_id'] . "'>
                    <button type='submit' class='btn text-white fw-bold'>X</button>
                </form>
            </div>
        </div>";
    }
} else {
    echo "
    <div class='row m-1 text-center'>
        <div class='col-12'>
            <h5 class='text-danger'>لا توجد بيانات للعرض</h5>
        </div>
    </div>";
}

$stmt->close();
$conn->close();
?>
