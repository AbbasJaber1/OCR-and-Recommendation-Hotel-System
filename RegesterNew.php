<?php
session_start();
include 'connect.php';

$guestName = '';
$message = '';
$msgType = 'g';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guestName = $_POST['guest_name'];
    $role = $_POST['role'];

    if (isset($_FILES['guest_images']) && !empty($_FILES['guest_images']['name'][0])) {
        $uploadDir = './label/';
        $guestFolder = $uploadDir . $guestName . '/';

        if (!is_dir($guestFolder)) {
            mkdir($guestFolder, 0777, true);
        }

        $imagePaths = [];

        for ($i = 0; $i < count($_FILES['guest_images']['name']); $i++) {
            if ($_FILES['guest_images']['error'][$i] === UPLOAD_ERR_OK) {
                $imageName = ($i + 1) . '.png';
                $imagePath = $guestFolder . $imageName;

                if (move_uploaded_file($_FILES['guest_images']['tmp_name'][$i], $imagePath)) {
                    $imagePaths[] = $imagePath;
                } else {
                    $message = "خطأ في تحميل الصورة " . ($i + 1);
                    $msgType = 'r';
                    break;
                }
            } else {
                $message = "خطأ في تحميل الصورة " . ($i + 1);
                $msgType = 'r';
                break;
            }
        }

        if (empty($message)) {
            $imagePathsString = implode(',', $imagePaths);
            $insertSql = "INSERT INTO guests (guest_name, role, image_paths) VALUES ('$guestName', '$role', '$imagePathsString')";

            if ($conn->query($insertSql) === TRUE) {
                $message = "تم تسجيل الموظف بنجاح";
                $msgType = 'g';
                $guestName = '';
            } else {
                $message = "خطأ في تسجيل الضيف: " . $conn->error;
                $msgType = 'r';
            }
        }
    } else {
        $message = "لم يتم تحميل الصور";
        $msgType = 'r';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <title>تسجيل الجدد — الفندق</title>
  <?php include 'includes/head.php'; ?>
</head>
<body>
<div class="hs-app">
  <?php include 'includes/sidebar.php'; ?>

  <div class="hs-main" id="mainContent">
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title" data-i18n="rn_title">تسجيل موظف جديد</div>
          <div class="hs-pg-sub" data-i18n="rn_sub">إضافة موظف جديد مع صوره لنظام التعرف على الوجه</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill">
          <div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div>
          <span class="hs-uname">مدير</span>
        </div>
      </div>
    </header>

    <main class="hs-content hs-stagger">

      <?php if($message): ?>
        <div class="hs-alert hs-alert-<?= $msgType ?> hs-mb-6">
          <i class="hs-alert-ic fas <?= $msgType==='g'?'fa-check-circle':'fa-exclamation-circle' ?>"></i>
          <span><?= htmlspecialchars($message) ?></span>
        </div>
      <?php endif; ?>

      <div class="hs-g2" style="gap:28px;align-items:start">

        <!-- Registration Form -->
        <div class="hs-card">
          <div class="hs-card-hd">
            <div class="hs-card-title">
              <div class="hs-card-ic"><i class="fas fa-user-plus"></i></div>
              <span data-i18n="rn_title">تسجيل موظف جديد</span>
            </div>
          </div>
          <div class="hs-card-bd">
            <form method="POST" enctype="multipart/form-data">

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">الاسم الثلاثي</label>
                <input class="hs-input" type="text" name="guest_name"
                       placeholder="ادخل الاسم الثلاثي"
                       value="<?= htmlspecialchars($guestName) ?>" required>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">الوظيفة</label>
                <input class="hs-input" type="text" name="role" id="role"
                       placeholder="مثال: admin, chef, receptionist" required>
                <p class="hs-help">الأدوار المتاحة: admin · chef · receptionist</p>
              </div>

              <div class="hs-form-g">
                <label class="hs-lbl hs-lbl-req">صور الموظف (حتى 4 صور)</label>
                <div class="hs-upload" id="uploadZone" role="button" tabindex="0">
                  <div class="hs-upload-ic"><i class="fas fa-camera"></i></div>
                  <div style="font-size:.9rem;font-weight:600;color:var(--tx-2);margin-bottom:4px">اسحب الصور هنا أو انقر للاختيار</div>
                  <div style="font-size:.8rem;color:var(--tx-3)">PNG أو JPG — حتى 4 صور للتعرف على الوجه</div>
                  <input type="file" id="guest_images" name="guest_images[]"
                         accept="image/*" multiple required style="display:none">
                </div>
                <div id="previewGrid" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:14px"></div>
              </div>

              <button type="submit" class="hs-btn hs-btn-primary hs-btn-block">
                <i class="fas fa-user-plus"></i> تسجيل الموظف
              </button>
            </form>
          </div>
        </div>

        <!-- Guidelines & Info -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:var(--au-50);color:var(--au-700)"><i class="fas fa-lightbulb"></i></div>
                إرشادات التسجيل
              </div>
            </div>
            <div class="hs-card-bd">
              <ul style="padding-right:18px;color:var(--tx-2);font-size:.875rem;line-height:2.2;margin:0">
                <li>التقط الصور من الأمام واليمين واليسار</li>
                <li>تأكد من وضوح الوجه بلا إضاءة خلفية</li>
                <li>تجنب النظارات أو أغطية الرأس في الصور</li>
                <li>الإضاءة الجيدة ضرورية لدقة التعرف</li>
                <li>الحد الأقصى 4 صور لكل موظف</li>
              </ul>
            </div>
          </div>

          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:#EFF6FF;color:#1D4ED8"><i class="fas fa-shield-alt"></i></div>
                أمان البيانات
              </div>
            </div>
            <div class="hs-card-bd">
              <p style="color:var(--tx-2);font-size:.875rem;line-height:1.8;margin:0">
                الصور تُحفظ محلياً في مجلد <code style="background:var(--s-2);padding:2px 6px;border-radius:4px;font-size:.8rem">label/</code> وتُستخدم حصراً لنظام التعرف على الوجه.
              </p>
            </div>
          </div>

          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic"><i class="fas fa-users-cog"></i></div>
                الأدوار في النظام
              </div>
            </div>
            <div class="hs-card-bd">
              <div style="display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;align-items:center;gap:10px">
                  <span class="hs-badge hs-badge-r">admin</span>
                  <span style="font-size:.85rem;color:var(--tx-2)">صلاحيات كاملة للنظام</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                  <span class="hs-badge hs-badge-au">chef</span>
                  <span style="font-size:.85rem;color:var(--tx-2)">طاقم المطبخ</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                  <span class="hs-badge hs-badge-b">receptionist</span>
                  <span style="font-size:.85rem;color:var(--tx-2)">موظف الاستقبال</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const uploadZone = document.getElementById('uploadZone');
const fileInput  = document.getElementById('guest_images');

uploadZone.addEventListener('click', () => fileInput.click());
uploadZone.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' ') fileInput.click(); });

uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('drag-over'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
uploadZone.addEventListener('drop', e => {
  e.preventDefault();
  uploadZone.classList.remove('drag-over');
  const dt = new DataTransfer();
  [...e.dataTransfer.files].slice(0,4).forEach(f => dt.items.add(f));
  fileInput.files = dt.files;
  previewFiles(dt.files);
});

fileInput.addEventListener('change', function() { previewFiles(this.files); });

function previewFiles(files) {
  const grid = document.getElementById('previewGrid');
  grid.innerHTML = '';
  [...files].slice(0,4).forEach((file, i) => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.style.cssText = 'position:relative;width:88px;height:88px;border-radius:12px;overflow:hidden;border:2px solid var(--g-300);box-shadow:var(--sh-sm)';
      div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">
        <span style="position:absolute;bottom:4px;right:5px;background:rgba(0,0,0,.65);color:#fff;font-size:.7rem;padding:1px 5px;border-radius:4px;font-weight:700">${i+1}</span>`;
      grid.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
  if(files.length > 0) uploadZone.classList.add('has-file');
  Lang.apply();
}
</script>
</body>
</html>
<?php $conn->close(); ?>
