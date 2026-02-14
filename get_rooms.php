<?php
$host = "localhost";
$user = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$database = "hotel-check-in/out"; // Change this to your actual database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['floor'])) {
    $floor = intval($_POST['floor']);
    
    // Fetch only available rooms for the selected floor
    $sql = "SELECT room_number FROM rooms WHERE room_number BETWEEN ? AND ? AND availability = 'YES'";
    $stmt = $conn->prepare($sql);
    
    $start = $floor * 100 + 1;  // Example: Floor 1 → 101, Floor 2 → 201
    $end = ($floor * 100) + 20; // Example: Floor 1 → 120, Floor 2 → 220

    $stmt->bind_param("ii", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<option value="">اختر الغرفة</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['room_number'] . '" name="'. $row['room_number'] .'" id="'. $row['room_number'] .'">' . $row['room_number'] . '</option>';
    }
}

$conn->close();
?>
