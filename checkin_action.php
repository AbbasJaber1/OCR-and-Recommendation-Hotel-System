<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $logId = $_POST['logId'];

    $sql = "UPDATE check_logs 
            SET return_time = CURRENT_TIMESTAMP 
            WHERE log_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logId);

    if ($stmt->execute()) {
        echo "Check-in successful";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
