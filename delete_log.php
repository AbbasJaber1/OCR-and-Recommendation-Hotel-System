<?php
include 'connect.php';  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_id = $_POST['log_id'];

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM check_logs WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $log_id);

    if ($stmt->execute()) {
        // Redirect back to the DailyReport.php after deletion
        header("Location: DailyReport.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
