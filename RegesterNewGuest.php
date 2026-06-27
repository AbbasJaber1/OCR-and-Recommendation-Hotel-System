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
    #video-container {
      width: 80vw;
      aspect-ratio: 3009 / 409;
      overflow: hidden;
      border-radius: 10px;
      border: 2px solid var(--g-400);
      box-shadow: 0 0 40px rgba(46,125,50,.4);
    }
    #video-container video {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
    }
    #button-container {
      display: flex;
      gap: 14px;
    }
    #capture {
      background: var(--g-600);
      color: #fff;
      border: none;
      padding: 10px 28px;
      border-radius: 10px;
      font-size: .95rem;
      font-family: inherit;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    #close-popup {
      background: #DC2626;
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 10px;
      font-size: .95rem;
      font-family: inherit;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
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
        HsToast.error(Lang.t('please_select_image'));
        return;
      }

      let formData = new FormData();
      formData.append("image", file);

      document.getElementById('status').innerText = Lang.t('processing_ocr');

      let response = await fetch("process_ocr.php", {
        method: "POST",
        body: formData
      });

      let result = await response.json();
      document.getElementById('status').innerText = Lang.t('ocr_completed');

      if (result.error) {
        document.getElementById('status').innerText = Lang.t('error_message') + ": " + result.error;
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
      HsToast.success(Lang.t('data_submitted'));
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
          HsToast.error(Lang.t('webcam_error'));
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
              Lang.apply();
            }
          });
        } else {
          $('#room_number').html('<option value="" data-i18n="select_room">اختر الغرفة</option>');
          Lang.apply();
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
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;flex-direction:column;align-items:center;gap:18px;width:80vw">
      <div id="video-container">
        <video id="video" autoplay></video>
      </div>
      <div id="button-container">
        <button id="capture" onclick="captureImage()"><i class="fas fa-camera"></i> <span data-i18n="capture_btn">التقاط</span></button>
        <button id="close-popup" onclick="closeWebcam()"><i class="fas fa-times"></i> <span data-i18n="close_btn">إغلاق</span></button>
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
                <span data-i18n="passport_scanner">ماسح جواز السفر</span>
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
                  <div style="color:rgba(255,255,255,.45);font-size:.8rem" data-i18n="mrz_area">منطقة المسح الضوئي MRZ</div>
                </div>
              </div>

              <!-- File upload zone -->
              <div class="hs-upload" id="ocrUploadZone"
                   onclick="document.getElementById('upload').click()"
                   style="padding:22px;margin-bottom:14px">
                <div class="hs-upload-ic" style="width:40px;height:40px;font-size:17px;margin-bottom:8px"><i class="fas fa-cloud-upload-alt"></i></div>
                <div style="font-size:.875rem;font-weight:600;color:var(--tx-2);margin-bottom:3px" data-i18n="upload_passport">رفع صورة جواز السفر</div>
                <div style="font-size:.78rem;color:var(--tx-3)" data-i18n="png_jpg">PNG أو JPG</div>
                <input type="file" id="upload" accept="image/*" style="display:none">
              </div>

              <!-- Status -->
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--r-md);background:var(--s-2);border:1px solid var(--bd-1);margin-bottom:16px">
                <i class="fas fa-circle-notch" style="color:var(--g-600)"></i>
                <span id="status" style="font-size:.85rem;color:var(--tx-2)" data-i18n="ready_scan">جاهز لمسح الجواز...</span>
              </div>

              <!-- Action buttons -->
              <div style="display:flex;flex-direction:column;gap:10px">
                <button type="button" class="hs-btn hs-btn-primary hs-btn-block" onclick="uploadImage()">
                  <i class="fas fa-magic"></i> <span data-i18n="scan_passport">مسح جواز السفر</span>
                </button>
                <button type="button" class="hs-btn hs-btn-sec hs-btn-block" onclick="openWebcam()">
                  <i class="fas fa-video"></i> <span data-i18n="capture_camera">التقاط عبر الكاميرا</span>
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
              <span data-i18n="passport_data">بيانات جواز السفر</span>
            </div>
            <span class="hs-badge hs-badge-g"><i class="fas fa-edit"></i> <span data-i18n="editable">قابل للتعديل</span></span>
          </div>
          <div class="hs-card-bd">

            <form id="passport_form" method="POST">
              <input type="hidden" name="full_name" id="full_name">

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="full_name_title">الاسم الكريم</label>
                  <input class="hs-input" type="text" id="first_name" placeholder="الاسم الأول" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="family_name">اسم العائلة</label>
                  <input class="hs-input" type="text" id="last_name" placeholder="اسم العائلة" required>
                </div>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req" data-i18n="nationality_label">الجنسية</label>
                <input class="hs-input" type="text" name="nationality" id="nationality"
                       placeholder="ادخل الجنسية" required>
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="birthdate_label">تاريخ الولادة</label>
                  <input class="hs-input" type="text" name="birth_date" id="dob"
                         placeholder="YYYY-MM-DD" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="gender_label">الجنس</label>
                  <input class="hs-input" type="text" name="gender" id="gender"
                         placeholder="M / F" required>
                </div>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req" data-i18n="passport_num_label">رقم جواز السفر</label>
                <input class="hs-input" type="text" name="passport_number" id="passport_number"
                       placeholder="ادخل رقم الجواز" required>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req" data-i18n="passport_exp_label">صلاحية جواز السفر</label>
                <input class="hs-input" type="text" name="passport_expiry" id="expiry_date"
                       placeholder="YYYY-MM-DD" required>
              </div>

              <div class="hs-divider"></div>

              <div style="font-size:.85rem;font-weight:700;color:var(--tx-2);margin-bottom:16px;display:flex;align-items:center;gap:6px">
                <i class="fas fa-door-open" style="color:var(--g-600)"></i> <span data-i18n="assign_room">تخصيص الغرفة</span>
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl" data-i18n="floor_label">الطابق</label>
                  <select class="hs-sel" id="floorSelect">
                    <option value="" data-i18n="select_floor">اختر الطابق</option>
                    <option value="1" data-i18n="floor_1">الطابق 1</option>
                    <option value="2" data-i18n="floor_2">الطابق 2</option>
                    <option value="3" data-i18n="floor_3">الطابق 3</option>
                    <option value="4" data-i18n="floor_4">الطابق 4</option>
                  </select>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="room_num">رقم الغرفة</label>
                  <select class="hs-sel" name="room_number" id="room_number">
                    <option value="" data-i18n="select_room">اختر الغرفة</option>
                  </select>
                </div>
              </div>

              <div class="hs-g2" style="gap:14px">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="checkin_date">تاريخ الدخول</label>
                  <input class="hs-input" type="date" name="check_in" id="check_in" required>
                </div>
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="checkout_date">تاريخ المغادرة</label>
                  <input class="hs-input" type="date" name="check_out" id="check_out" required>
                </div>
              </div>

              <button type="button" class="hs-btn hs-btn-gold hs-btn-block" onclick="submitForm()">
                <i class="fas fa-paper-plane"></i> <span data-i18n="confirm_registration">تأكيد التسجيل</span>
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
