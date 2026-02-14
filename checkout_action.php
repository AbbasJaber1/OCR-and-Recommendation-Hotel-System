<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $guestName = $_POST['guestName'];

    // Prepare the SQL statement
    $sql = "INSERT INTO check_logs (guest_id, checkout_time)
            VALUES ((SELECT guest_id FROM guests WHERE guest_name = ?), CURRENT_TIMESTAMP)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $guestName); // only one string param now

    if ($stmt->execute()) {
        echo "✅ Checkout logged";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
