<?php

$host = "localhost";
$user = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$database = "hotel-check-in/out"; // Change this to your actual database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database Connection Failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["full_name"]) || !isset($_POST["nationality"]) ||
        !isset($_POST["passport_number"]) || !isset($_POST["birth_date"]) || 
        !isset($_POST["gender"]) || !isset($_POST["passport_expiry"]) || !isset($_POST["room_number"]) ||
        !isset($_POST["check_in"]) || !isset($_POST["check_out"])) {
        die(json_encode(["error" => "Missing required fields"]));
    }

    $full_name = $conn->real_escape_string($_POST["full_name"]);
    $nationality = $conn->real_escape_string($_POST["nationality"]);
    $passport_number = $conn->real_escape_string($_POST["passport_number"]);
    $birth_date = $conn->real_escape_string($_POST["birth_date"]);
    $gender = $conn->real_escape_string($_POST["gender"]);
    $passport_expiry = $conn->real_escape_string($_POST["passport_expiry"]);
    $room_number = (int)$_POST["room_number"];
    $check_in = $conn->real_escape_string($_POST["check_in"]);
    $check_out = $conn->real_escape_string($_POST["check_out"]);

    $sql = "INSERT INTO Real_Guests (full_name, nationality, passport_number, birth_date, gender, passport_expiry, room_number, check_in, check_out) 
            VALUES ('$full_name', '$nationality', '$passport_number', '$birth_date', '$gender', '$passport_expiry', $room_number, '$check_in', '$check_out')";

    if ($conn->query($sql) === TRUE) {
        // Update room availability
        $updateRoomSQL = "UPDATE rooms SET availability='NO' WHERE room_number=$room_number";
        $conn->query($updateRoomSQL);

    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الجدد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
          <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<!--Newly Added-->

<style>
        #webcam-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            height: 20vh;
            background: white;
            border: 2px solid black;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
        }
        #video-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .action-button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: white;
        }
        #capture {
            background-color: #007bff;
        }
        #close-popup {
            background-color: red;
        }
        #captured-image {
            width: 80vw;
            height: 20vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            border: 2px solid black;
        }
        #captured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    <script>
        async function uploadImage() {
            let fileInput = document.getElementById('upload');
            let file = fileInput.files[0];

            if (!file) {
                alert("Please select an image first.");
                return;
            }

            let formData = new FormData();
            formData.append("image", file);

            document.getElementById('status').innerText = "Processing OCR...";

            let response = await fetch("process_ocr.php", {
                method: "POST",
                body: formData
            });

            let result = await response.json();
            document.getElementById('status').innerText = "OCR Completed!";

            if (result.error) {
                document.getElementById('output').innerHTML = `<p style="color: red;">Error: ${result.error}</p>`;
                return;
            }

            document.getElementById('first_name').value = result["First Name"];
            document.getElementById('last_name').value = result["Last Name"];
            document.getElementById('nationality').value = result["Nationality"];
            document.getElementById('passport_number').value = result["Passport Number"];
            document.getElementById('dob').value = result["Date of Birth"];
            document.getElementById('gender').value = result["Gender"];
            document.getElementById('expiry_date').value = result["Expiry Date"];

        }

        function submitForm() {
            let data = {
                "First Name": document.getElementById('first_name').value,
                "Last Name": document.getElementById('last_name').value,
                "Nationality": document.getElementById('nationality').value,
                "Passport Number": document.getElementById('passport_number').value,
                "Date of Birth": document.getElementById('dob').value,
                "Gender": document.getElementById('gender').value,
                "Expiry Date": document.getElementById('expiry_date').value
            };
            document.getElementById('full_name').value = document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value;
            document.getElementById('passport_form').submit();
            
            console.log("Form Submitted with Data:", data);
            alert("Data has been submitted successfully! (Check console for details)");
        }
        
        let videoStream;

        function openWebcam() {
            let popup = document.getElementById('webcam-popup');
            popup.style.display = "block";

            let video = document.getElementById('video');
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                })
                .catch(error => {
                    console.error("Error accessing webcam:", error);
                    alert("Could not access webcam.");
                });
        }

        function captureImage() {
            let video = document.getElementById('video');
            let videoWidth = video.videoWidth;
            let videoHeight = video.videoHeight;

            let cropWidth = videoWidth; 
            let cropHeight = videoHeight * 0.2; 
            let cropX = 0;
            let cropY = (videoHeight - cropHeight) / 2; 

            let canvas = document.createElement('canvas');
            canvas.width = cropWidth;
            canvas.height = cropHeight;
            let ctx = canvas.getContext('2d');

            ctx.drawImage(video, cropX, cropY, cropWidth, cropHeight, 0, 0, canvas.width, canvas.height);

            // Convert canvas data to Blob
            canvas.toBlob(blob => {
                let file = new File([blob], "Captured_MRZ.png", { type: "image/png" });

                let fileInput = document.getElementById('upload');
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                document.getElementById('captured-image').innerHTML = `<img src="${URL.createObjectURL(blob)}">`;

                console.log("Captured image assigned to file input.");
            }, "image/png");

            closeWebcam();
        }


        function closeWebcam() {
            let popup = document.getElementById('webcam-popup');
            popup.style.display = "none";

            if (videoStream) {
                let tracks = videoStream.getTracks();
                tracks.forEach(track => track.stop());
            }
        }

        $(document).ready(function () {
                        $('#floorSelect').change(function () {
                            var floor = $(this).val();
                            if (floor) {
                                $.ajax({
                                    url: 'get_rooms.php',
                                    type: 'POST',
                                    data: { floor: floor },
                                    success: function (response) {
                                    $('#room_number').html(response);
                                }
                            });
                        } else {
                            $('#room_number').html('<option value="">اختر الغرفة</option>');
                        }
                    });
                });

    </script>

<!--Newly Added-->

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

            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئسية</a></li>
            
        </ul>
    </div>
</nav>


    <!--Newly Added-->


    <!-- <h1>Passport MRZ Scanner</h1>
    <input type="file" id="upload" accept="image/*">
    <button onclick="uploadImage()">Scan Passport</button>
    <p id="status">Waiting for input...</p> -->
<!-- 
    <form id="passport_form" action="RegesterNewGuest.php" method="POST">
        <h2>Passport Details (Editable)</h2>
        
        <input type="hidden" name="full_name" id="full_name">

        <label>First Name:</label>
        <input type="text" id="first_name"><br><br>

        <label>Last Name:</label>
        <input type="text" id="last_name"><br><br>

        <label>Nationality:</label>
        <input type="text" name="nationality" id="nationality"><br><br>

        <label>Passport Number:</label>
        <input type="text" name="passport_number" id="passport_number"><br><br>

        <label>Date of Birth:</label>
        <input type="text" name="birth_date" id="dob"><br><br>

        <label>Gender:</label>
        <input type="text" name="gender" id="gender"><br><br>

        <label>Expiry Date:</label>
        <input type="text" name="passport_expiry" id="expiry_date"><br><br>

        <label>Choose Room Number:</label>
        <input type="text" name="room_number" id="room_number"><br><br>

        <button type="button" onclick="submitForm()">Submit</button>

    </form> -->

    <!-- Button to open the webcam -->
    <!-- <button onclick="openWebcam()">Open Webcam</button> -->

    <!-- Webcam popup -->
    <div id="webcam-popup" style="display:none;">
        <div id="video-container">
            <video id="video" autoplay></video>
        </div>
        <div id="button-container">
            <button id="capture" class="action-button" onclick="captureImage()">Capture</button>
            <button id="close-popup" class="action-button" onclick="closeWebcam()">Close</button>
        </div>
    </div>

    <!--Newly Added-->
<!-- Body Parts -->
<div class="container mt-5">
    <form id="passport_form" class="p-4 border rounded-4 shadow-sm bg-white" method="POST" enctype="multipart/form-data" dir="rtl">
        
        <h2 class="mb-4 text-center">تسجيل الجدد</h2>
       
        <p style="margin-right: 77%;"> ادخل صورة الجواز : Passport MRZ Scanner</p>
        <input type="file" id="upload" accept="image/*" style="margin-right: 80%;" class="btn btn-success w-100 mb-2" >
        
        <p id="status" style="margin-right: 80%;">Waiting for input</p>

        <input type="hidden" name="full_name" id="full_name">

        <div class="mb-3">
            <label for="name" class="form-label">الاسم الكريم</label>
            <input class="form-control" type="text" id="first_name" placeholder="ادخل الاسم" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">اسم العائلة</label>
            <input class="form-control" type="text" id="last_name" placeholder="ادخل الاسم" required>
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">الجنسية</label>
            <input class="form-control" type="text" name="nationality" id="nationality" placeholder="ادخل الجنسية" required>
        </div>
        <div class="mb-3">
            <label for="Date_Of_Birth" class="form-label">تاريخ الولادة</label>
            <input class="form-control" type="text" name="birth_date" id="dob" placeholder="ادخل تاريخ الولاد" required>
        </div>
        <div class="mb-3">
            <label for="Passport_Expiry" class="form-label">صالحية جواز السفر</label>
            <input class="form-control" type="text" name="passport_expiry" id="expiry_date" placeholder=" ادخل صالحية جواز السفر" required>
        </div>
        <div class="mb-3">
            <label for="Passport_Number" class="form-label">رقم جواز السفر</label>
            <input class="form-control" type="text" name="passport_number" id="passport_number" placeholder="ادخل رقم جواز السفر" required>
        </div>
        <div class="mb-3">
            <label for="Gender" class="form-label">الجنس</label>
            <input class="form-control" type="text" name="gender" id="gender" placeholder="ادخل الجنس" required>
        </div>
        <!-- <div class="mb-3">
            <label for="room_number" class="form-label">رقم الغرفة</label>
            <input class="form-control" type="text" name="room_number" id="room_number" placeholder="ادخل رقم الغرفة" required>
        </div> -->

        <!-- Newly added Drop downs -->

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">اختر الطابق</label>
                <select class="form-select" id="floorSelect">
                <option value="">اختر الطابق</option>
                <option value="1">الطابق 1</option>
                <option value="2">الطابق 2</option>
                <option value="3">الطابق 3</option>
                <option value="4">الطابق 4</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">اختر الغرفة</label>
                <select class="form-select"  name="room_number" id="room_number">
                <option value="">اختر الغرفة</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="check_in" class="form-label">تسجيل الدخول:</label>
                <input type="date" id="check_in" name="check_in" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="check_out" class="form-label">تسجيل الخروج:</label>
                <input type="date" id="check_out" name="check_out" class="form-control" required>
            </div>
        </div>
        <!-- Newly added Drop downs -->

            <!-- Scanning Passport button -->
            <button type="button" class="btn btn-success w-100 mb-2" onclick="uploadImage()">مسح جواز السفر </button>
            <!-- Button to open the webcam -->
            <button type="button" class="btn btn-success w-100 mb-2" onclick="openWebcam()">Open Webcam</button>

            <button type="button" class="btn btn-success w-100" onclick="submitForm()">ارسل</button>
        </form>
</div>

<!-- Captured image display -->
<div id="captured-image" class="container mt-5"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>