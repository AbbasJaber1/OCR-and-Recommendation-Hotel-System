<?php

require_once __DIR__ . '/env-loader.php';

$host = env('DB_HOST', 'localhost');
$user = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');
$database = env('DB_NAME', 'hotel-check-in/out');

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
        $updateRoomSQL = "UPDATE rooms SET availability='NO' WHERE room_number=$room_number";
        $conn->query($updateRoomSQL);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <title>تسجيل ضيف جديد — الفندق</title>
  <?php include 'includes/head.php'; ?>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    #webcam-popup {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.88);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      z-index: 9999;
    }
    #captured-image img {
      border-radius: var(--r-lg);
      max-width: 100%;
      border: 2px solid var(--g-300);
      box-shadow: var(--sh-sm);
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
        document.getElementById('status').innerText = "Error: " + result.error;
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

    $(document).ready(function() {
      $('#floorSelect').change(function() {
        var floor = $(this).val();
        if (floor) {
          $.ajax({
            url: 'get_rooms.php',
            type: 'POST',
            data: { floor: floor },
            success: function(response) {
              $('#room_number').html(response);
            }
          });
        } else {
          $('#room_number').html('<option value="">اختر الغرفة</option>');
        }
      });
    });
  </script>
</head>
<body>
<div class="hs-app">
  <?php include 'includes/sidebar.php'; ?>

  <!-- Webcam Popup Overlay -->
  <div id="webcam-popup" style="display:none">
    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:min(680px,94vw)">
      <div style="text-align:center;margin-bottom:14px">
        <span style="color:rgba(255,255,255,.65);font-size:.9rem"><i class="fas fa-passport" style="color:var(--g-300);margin-left:6px"></i> وجّه الجواز نحو الكاميرا ثم التقط منطقة MRZ</span>
      </div>
      <div id="video-container" style="border-radius:16px;overflow:hidden;border:2px solid var(--g-400);box-shadow:0 0 40px rgba(46,125,50,.45)">
        <video id="video" autoplay style="width:100%;display:block"></video>
      </div>
      <div id="button-container" style="display:flex;justify-content:center;gap:14px;margin-top:18px">
        <button id="capture" onclick="captureImage()"
                style="background:var(--g-600);color:#fff;border:none;padding:12px 30px;border-radius:12px;font-size:1rem;font-family:inherit;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:8px">
          <i class="fas fa-camera"></i> التقاط
        </button>
        <button id="close-popup" onclick="closeWebcam()"
                style="background:#DC2626;color:#fff;border:none;padding:12px 26px;border-radius:12px;font-size:1rem;font-family:inherit;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:8px">
          <i class="fas fa-times"></i> إغلاق
        </button>
      </div>
    </div>
  </div>

  <div class="hs-main" id="mainContent">
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title">تسجيل ضيف جديد</div>
          <div class="hs-pg-sub">مسح جواز السفر وتسجيل بيانات الضيف</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill">
          <div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div>
          <span class="hs-uname">موظف</span>
        </div>
      </div>
    </header>

    <main class="hs-content hs-stagger">
      <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:28px;align-items:start">

        <!-- Scanner Column -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:#0D1B0D;color:var(--g-300)"><i class="fas fa-passport"></i></div>
                ماسح جواز السفر
              </div>
              <span class="hs-badge hs-badge-au"><i class="fas fa-magic"></i> OCR</span>
            </div>
            <div class="hs-card-bd">

              <!-- Animated OCR area -->
              <div class="hs-ocr-area" style="min-height:140px;margin-bottom:20px">
                <div class="hs-ocr-frame"></div>
                <div class="hs-ocr-scan"></div>
                <div class="hs-ocr-corner hs-ocr-c-tl"></div>
                <div class="hs-ocr-corner hs-ocr-c-tr"></div>
                <div class="hs-ocr-corner hs-ocr-c-bl"></div>
                <div class="hs-ocr-corner hs-ocr-c-br"></div>
                <div style="text-align:center;z-index:1;padding:16px">
                  <div style="font-size:2rem;color:var(--g-300);margin-bottom:6px;opacity:.45"><i class="fas fa-passport"></i></div>
                  <div style="color:rgba(255,255,255,.45);font-size:.8rem">منطقة المسح الضوئي MRZ</div>
                </div>
              </div>

              <!-- File upload zone -->
              <div class="hs-upload" id="ocrUploadZone"
                   onclick="document.getElementById('upload').click()"
                   style="padding:22px;margin-bottom:14px">
                <div class="hs-upload-ic" style="width:40px;height:40px;font-size:17px;margin-bottom:8px"><i class="fas fa-cloud-upload-alt"></i></div>
                <div style="font-size:.875rem;font-weight:600;color:var(--tx-2);margin-bottom:3px">رفع صورة جواز السفر</div>
                <div style="font-size:.78rem;color:var(--tx-3)">PNG أو JPG</div>
                <input type="file" id="upload" accept="image/*" style="display:none">
              </div>

              <!-- Status -->
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--r-md);background:var(--s-2);border:1px solid var(--bd-1);margin-bottom:16px">
                <i class="fas fa-circle-notch" style="color:var(--g-600)"></i>
                <span id="status" style="font-size:.85rem;color:var(--tx-2)">جاهز لمسح الجواز...</span>
              </div>

              <!-- Action buttons -->
              <div style="display:flex;flex-direction:column;gap:10px">
                <button type="button" class="hs-btn hs-btn-primary hs-btn-block" onclick="uploadImage()">
                  <i class="fas fa-magic"></i> مسح جواز السفر
                </button>
                <button type="button" class="hs-btn hs-btn-sec hs-btn-block" onclick="openWebcam()">
                  <i class="fas fa-video"></i> التقاط عبر الكاميرا
                </button>
              </div>

              <!-- Captured image preview -->
              <div id="captured-image" style="margin-top:14px"></div>

            </div>
          </div>

        </div>

        <!-- Passport Form Column -->
        <div class="hs-card">
          <div class="hs-card-hd">
            <div class="hs-card-title">
              <div class="hs-card-ic"><i class="fas fa-id-card"></i></div>
              بيانات جواز السفر
            </div>
            <span class="hs-badge hs-badge-g"><i class="fas fa-edit"></i> قابل للتعديل</span>
          </div>
          <div class="hs-card-bd">

            <form id="passport_form" method="POST">
              <input type="hidden" name="full_name" id="full_name">

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">الاسم الكريم</label>
                  <input class="hs-input" type="text" id="first_name" placeholder="الاسم الأول" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">اسم العائلة</label>
                  <input class="hs-input" type="text" id="last_name" placeholder="اسم العائلة" required>
                </div>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">الجنسية</label>
                <input class="hs-input" type="text" name="nationality" id="nationality"
                       placeholder="ادخل الجنسية" required>
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">تاريخ الولادة</label>
                  <input class="hs-input" type="text" name="birth_date" id="dob"
                         placeholder="YYYY-MM-DD" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">الجنس</label>
                  <input class="hs-input" type="text" name="gender" id="gender"
                         placeholder="M / F" required>
                </div>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">رقم جواز السفر</label>
                <input class="hs-input" type="text" name="passport_number" id="passport_number"
                       placeholder="ادخل رقم الجواز" required>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">صلاحية جواز السفر</label>
                <input class="hs-input" type="text" name="passport_expiry" id="expiry_date"
                       placeholder="YYYY-MM-DD" required>
              </div>

              <div class="hs-divider"></div>

              <div style="font-size:.85rem;font-weight:700;color:var(--tx-2);margin-bottom:16px;display:flex;align-items:center;gap:6px">
                <i class="fas fa-door-open" style="color:var(--g-600)"></i> تخصيص الغرفة
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl">الطابق</label>
                  <select class="hs-sel" id="floorSelect">
                    <option value="">اختر الطابق</option>
                    <option value="1">الطابق 1</option>
                    <option value="2">الطابق 2</option>
                    <option value="3">الطابق 3</option>
                    <option value="4">الطابق 4</option>
                  </select>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">رقم الغرفة</label>
                  <select class="hs-sel" name="room_number" id="room_number">
                    <option value="">اختر الغرفة</option>
                  </select>
                </div>
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">تاريخ الدخول</label>
                  <input class="hs-input" type="date" name="check_in" id="check_in" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req">تاريخ المغادرة</label>
                  <input class="hs-input" type="date" name="check_out" id="check_out" required>
                </div>
              </div>

              <button type="button" class="hs-btn hs-btn-gold hs-btn-block" onclick="submitForm()">
                <i class="fas fa-paper-plane"></i> تأكيد التسجيل
              </button>

            </form>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
