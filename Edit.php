<?php
include 'connect.php';

// Handle search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Initialize guest query
$guestQuery = "SELECT * FROM guests WHERE guest_name LIKE ?";
$stmt = $conn->prepare($guestQuery);
$searchParam = "%$search%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();
$guestResult = $stmt->get_result();

// Handle room update
if (isset($_POST['update_room'])) {
    $guestId = $_POST['guest_id'];
    $newRoom = $_POST['new_room'];

    $updateQuery = "UPDATE guests SET room_number = ? WHERE guest_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $newRoom, $guestId);
    $stmt->execute();

    echo "<script>alert('Room updated successfully!'); window.location.href='Edit.php';</script>";
}

// Handle new room insertion with duplicate check
if (isset($_POST['add_room'])) {
    $roomNumber = $_POST['room_number'];

    // Check if room already exists
    $checkQuery = "SELECT * FROM rooms WHERE room_number = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $roomNumber);
    $stmt->execute();
    $existingRoom = $stmt->get_result();

    if ($existingRoom->num_rows > 0) {
        echo "<script>alert('Room number already exists! Please choose a different number.');</script>";
    } else {
        $insertQuery = "INSERT INTO rooms (room_number) VALUES (?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("i", $roomNumber);
        $stmt->execute();
        echo "<script>alert('New room added!'); window.location.href='Edit.php';</script>";
    }
}

// Handle guest removal
if (isset($_POST['remove_guest'])) {
    $guestId = $_POST['guest_id'];

    $deleteQuery = "DELETE FROM guests WHERE guest_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $guestId);
    $stmt->execute();

    echo "<script>alert('Guest removed successfully!'); window.location.href='Edit.php';</script>";
}

// Fetch all available rooms for dropdown
$roomsQuery = "SELECT room_number FROM rooms";
$roomsResult = $conn->query($roomsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guest Information</title>
    <link rel="stylesheet" href="dailyreport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <li class="nav-item"><a class="nav-link text-white" href="CheckIn.php">تسجيل دخول</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckOut.php">تسجيل خروج</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="DailyReport.php">الكشف اليومي</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="RegesterNew.php">تسجيل الجدد</a></li></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئسية</a></li>

            
        </ul>
    </div>
</nav>

<!-- Body -->
<div class="container-md mt-4 p-3 border border-4 border-success bg-success">
    <h1 class="text-center mb-3 text-white">تعديل معلومات الضيف</h1>

    <div class="bg-success p-4 rounded">

        <!-- Search Form -->
        <form method="POST" class="mb-3 row ">
            <button type="submit" class="col-1 btn btn-light rounded-pill ms-5 me-2">بحث</button>
            <input class="col-10 rounded-pill border ms-3" type="text" name="search" 
                   placeholder="ابحث عن اسم الضيف" value="<?php echo htmlspecialchars($search); ?>" />
        </form>

        <!-- Display Guest Data (only if search is not empty) -->
        <?php if (!empty($search) && $guestResult->num_rows > 0): ?>
            <?php while ($guest = $guestResult->fetch_assoc()): ?>
                <form method="POST" class="row mb-2">
                    <input type="hidden" name="guest_id" value="<?php echo $guest['guest_id']; ?>" />
                    
                    <!-- Guest Name -->
                    <div class="col-4 text-center">
                        <h5 class="p-2 bg-white border rounded-pill"><?php echo $guest['guest_name']; ?></h5>
                    </div>

                    <!-- Room Selection -->
                    <div class="col-4 p-1">
                        <select name="new_room" class="form-select rounded-pill">
                            <?php 
                            $roomsResult->data_seek(0); 
                            while ($room = $roomsResult->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $room['room_number']; ?>" 
                                    <?php echo ($room['room_number'] == $guest['room_number']) ? 'selected' : ''; ?>>
                                    Room <?php echo $room['room_number']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Update and Remove Buttons -->
                    <div class="col-4 text-center">
                        <button type="submit" name="update_room" class="btn btn-light rounded-pill">ارسل</button>
                        <button type="submit" name="remove_guest" class="btn btn-danger rounded-pill">إزالة</button>
                    </div>
                </form>
            <?php endwhile; ?>
        <?php elseif (!empty($search)): ?>
            <div class="text-center text-white">لا توجد بيانات مطابقة</div>
        <?php endif; ?>

        <hr class="mt-4 mb-4">

        <!-- Add New Room Form -->
        <h3 class="text-center text-white mb-3">إضافة غرفة جديدة</h3>
        <form method="POST" class="row g-3">
            <div class="col-8">
                <input type="number" name="room_number" class="form-control rounded-pill" 
                       placeholder="أدخل رقم الغرفة" required />
            </div>
            <div class="col-4 text-center">
                <button type="submit" name="add_room" class="btn btn-light rounded-pill">إضافة</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
