<?php
include 'connect.php';  // Include database connection

// Initialize variables
$guestName = '';
// $roomNumber = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $guestName = $_POST['guest_name'];
    // $roomNumber = $_POST['room_number'];
    // $passwords = $_POST['passwords'];
    $role = $_POST['role'];

    // Check if room number exists in rooms table
    // $roomCheckSql = "SELECT * FROM rooms WHERE room_number = '$roomNumber'";
    // $roomCheckResult = $conn->query($roomCheckSql);

    
        // Handle image uploads
        if (isset($_FILES['guest_images']) && !empty($_FILES['guest_images']['name'][0])) {
            $uploadDir = './label/'; // Base directory for uploads
            $guestFolder = $uploadDir . $guestName . '/'; // Folder named after the guest

            // Create the guest folder if it doesn't exist
            if (!is_dir($guestFolder)) {
                mkdir($guestFolder, 0777, true);
            }

            // Initialize an array to store image paths
            $imagePaths = [];

            // Loop through each uploaded file
            for ($i = 0; $i < count($_FILES['guest_images']['name']); $i++) {
                if ($_FILES['guest_images']['error'][$i] === UPLOAD_ERR_OK) {
                    // Generate the image name (1.png, 2.png, etc.)
                    $imageName = ($i + 1) . '.png';
                    $imagePath = $guestFolder . $imageName;

                    // Move the uploaded file to the guest folder
                    if (move_uploaded_file($_FILES['guest_images']['tmp_name'][$i], $imagePath)) {
                        $imagePaths[] = $imagePath; // Save the path
                    } else {
                        $message = "خطأ في تحميل الصورة " . ($i + 1); // Error uploading image
                        break;
                    }
                } else {
                    $message = "خطأ في تحميل الصورة " . ($i + 1); // Error uploading image
                    break;
                }
            }

            // If all images were uploaded successfully
            if (empty($message)) {
                // Convert the image paths array to a comma-separated string
                $imagePathsString = implode(',', $imagePaths);

                // Insert new guest into the guests table with the image paths
                $insertSql = "INSERT INTO guests (guest_name, role, image_paths) 
                              VALUES ('$guestName', '$role', '$imagePathsString')";

                if ($conn->query($insertSql) === TRUE) {
                    $message = "تم تسجيل الموظف بنجاح"; // Guest registered successfully
                } else {
                    $message = "خطأ في تسجيل الضيف: " . $conn->error; // Error in registration
                }
            }
        } else {
            $message = "لم يتم تحميل الصور"; // No images uploaded
        }
    }


// $days = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
// $availabilityData = [];

// for ($i = 0; $i < 7; $i++) {
//   $date = date('Y-m-d', strtotime("+$i days")); // Get each day's date

//   $query = "
//       SELECT COUNT(*) as available_rooms 
//       FROM rooms 
//       WHERE availability='YES' 
//       AND room_number NOT IN (
//           SELECT room_number FROM real_guests 
//           WHERE ('$date' BETWEEN check_in AND check_out)
//       )
//   ";

//   $result = $conn->query($query);
//   $row = $result->fetch_assoc();
//   $availabilityData[] = $row["available_rooms"];
//}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الجدد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="../V5/bootstrap-5.3.3-dist/css/bootstrap.rtl.min.css" rel="stylesheet">

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success navbar-light">
    <div class="container-xxl ">
        <a class="navbar-brand" href="#">
        <img src="assets/logo/Full_logo.png" alt="" width="auto" height="30" center>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse justify-content-end pe-3 " id="main-nav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link text-white me-2" href="Edit.php">تعديل</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckOut.php">تسجيل خروج</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="DailyReport.php">الكشف اليومي</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="RegesterNew.php">تسجيل الجدد</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئسية</a></li>          
        </ul>
    </div>
</nav>

<!-- Body Parts -->
<div class="container mt-5 mb-5">
    <form class="p-4 border rounded-4 shadow-sm bg-white" method="POST" enctype="multipart/form-data" dir="rtl">
        <h2 class="mb-4 text-center">تسجيل الجدد</h2>

        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">الاسم الثلاثي</label>
            <input class="form-control" id="name" name="guest_name" placeholder="ادخل الاسم الثلاثي" required>
        </div>
        <!-- <div class="mb-3">
            <label for="room_number" class="form-label">رقم الغرفة</label>
            <input class="form-control" id="room_number" name="room_number" placeholder="ادخل رقم الغرفة" required>
        </div>
        <div class="mb-3">
            <label for="passwords" class="form-label">كلمة السر</label>
            <input class="form-control" id="passwords" name="passwords" placeholder="ادخل كلمة المرور" required>
        </div> -->
        <div class="mb-3">
            <label for="passwords" class="form-label">الوظيفة</label>
            <input class="form-control" id="role" name="role" placeholder="role: admin, chef, receptionist" required>
        </div>
        <div class="mb-3">
            <label for="guest_images" class="form-label">صور الضيف (يمكن تحميل حتى 4 صور)</label>
            <input type="file" class="form-control" id="guest_images" name="guest_images[]" accept="image/*" multiple required>
        </div>
        <button type="submit" class="btn btn-success w-100">ارسل</button>
    </form>
</div>

<!--Newly added-->
<!-- 

      
      <div class="table-responsive mb-5 mt-5">
      <h2>Rooms Details</h2>
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">عنوان</th>
              <th scope="col">عنوان</th>
              <th scope="col">عنوان</th>
              <th scope="col">عنوان</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1,001</td>
              <td>بيانات</td>
              <td>عشوائية</td>
              <td>تثري</td>
              <td>الجدول</td>
            </tr>
            <tr>
              <td>1,002</td>
              <td>تثري</td>
              <td>مبهة</td>
              <td>تصميم</td>
              <td>تنسيق</td>
            </tr>
            <tr>
              <td>1,003</td>
              <td>عشوائية</td>
              <td>غنية</td>
              <td>قيمة</td>
              <td>مفيدة</td>
            </tr>
            <tr>
              <td>1,003</td>
              <td>معلومات</td>
              <td>تثري</td>
              <td>توضيحية</td>
              <td>عشوائية</td>
            </tr>
            <tr>
              <td>1,004</td>
              <td>الجدول</td>
              <td>بيانات</td>
              <td>تنسيق</td>
              <td>قيمة</td>
            </tr>
            <tr>
              <td>1,005</td>
              <td>قيمة</td>
              <td>مبهة</td>
              <td>الجدول</td>
              <td>تثري</td>
            </tr>
            <tr>
              <td>1,006</td>
              <td>قيمة</td>
              <td>توضيحية</td>
              <td>غنية</td>
              <td>عشوائية</td>
            </tr>
            <tr>
              <td>1,007</td>
              <td>تثري</td>
              <td>مفيدة</td>
              <td>معلومات</td>
              <td>مبهة</td>
            </tr>
            <tr>
              <td>1,008</td>
              <td>بيانات</td>
              <td>عشوائية</td>
              <td>تثري</td>
              <td>الجدول</td>
            </tr>
            <tr>
              <td>1,009</td>
              <td>تثري</td>
              <td>مبهة</td>
              <td>تصميم</td>
              <td>تنسيق</td>
            </tr>
            <tr>
              <td>1,010</td>
              <td>عشوائية</td>
              <td>غنية</td>
              <td>قيمة</td>
              <td>مفيدة</td>
            </tr>
            <tr>
              <td>1,011</td>
              <td>معلومات</td>
              <td>تثري</td>
              <td>توضيحية</td>
              <td>عشوائية</td>
            </tr>
            <tr>
              <td>1,012</td>
              <td>الجدول</td>
              <td>تثري</td>
              <td>تنسيق</td>
              <td>قيمة</td>
            </tr>
            <tr>
              <td>1,013</td>
              <td>قيمة</td>
              <td>مبهة</td>
              <td>الجدول</td>
              <td>تصميم</td>
            </tr>
            <tr>
              <td>1,014</td>
              <td>قيمة</td>
              <td>توضيحية</td>
              <td>غنية</td>
              <td>عشوائية</td>
            </tr>
            <tr>
              <td>1,015</td>
              <td>بيانات</td>
              <td>مفيدة</td>
              <td>معلومات</td>
              <td>الجدول</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div> -->

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>