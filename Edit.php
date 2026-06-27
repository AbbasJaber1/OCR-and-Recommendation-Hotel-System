<?php
session_start();
include 'connect.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';
$guestResult = null;
$msg = '';
$msgType = 'g';

$guestQuery = "SELECT * FROM guests WHERE guest_name LIKE ?";
$stmt = $conn->prepare($guestQuery);
$sp = "%$search%";
$stmt->bind_param("s", $sp);
$stmt->execute();
$guestResult = $stmt->get_result();

if (isset($_POST['update_room'])) {
    $guestId = $_POST['guest_id'];
    $newRoom  = $_POST['new_room'];
    $s = $conn->prepare("UPDATE guests SET room_number=? WHERE guest_id=?");
    $s->bind_param("ii", $newRoom, $guestId);
    $s->execute();
    $msg = 'تم تحديث الغرفة بنجاح'; $msgType = 'g';
}

if (isset($_POST['add_room'])) {
    $roomNumber = $_POST['room_number'];
    $sc = $conn->prepare("SELECT * FROM rooms WHERE room_number=?");
    $sc->bind_param("i", $roomNumber);
    $sc->execute();
    if($sc->get_result()->num_rows > 0) {
        $msg = 'رقم الغرفة موجود بالفعل!'; $msgType = 'r';
    } else {
        $si = $conn->prepare("INSERT INTO rooms (room_number) VALUES (?)");
        $si->bind_param("i", $roomNumber);
        $si->execute();
        $msg = 'تمت إضافة الغرفة بنجاح'; $msgType = 'g';
    }
}

if (isset($_POST['remove_guest'])) {
    $guestId = $_POST['guest_id'];
    $sd = $conn->prepare("DELETE FROM guests WHERE guest_id=?");
    $sd->bind_param("i", $guestId);
    $sd->execute();
    $msg = 'تم حذف الموظف بنجاح'; $msgType = 'g';
    $search = '';
    $sp = '%%';
    $stmt->bind_param("s", $sp);
    $stmt->execute();
    $guestResult = $stmt->get_result();
}

$roomsResult = $conn->query("SELECT room_number FROM rooms ORDER BY room_number");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <title>إدارة الغرف والضيوف — الفندق</title>
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
          <div class="hs-pg-title" data-i18n="edt_title">إدارة الغرف والضيوف</div>
          <div class="hs-pg-sub" data-i18n="edt_sub">تعديل بيانات الموظفين وإدارة الغرف</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill"><div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div><span class="hs-uname">مدير</span></div>
      </div>
    </header>

    <main class="hs-content hs-stagger">

      <?php if($msg): ?>
        <div class="hs-alert hs-alert-<?= $msgType ?> hs-mb-6">
          <i class="hs-alert-ic fas <?= $msgType==='g'?'fa-check-circle':'fa-exclamation-circle' ?>"></i>
          <span><?= $msg ?></span>
        </div>
      <?php endif; ?>

      <div class="hs-g2" style="gap:24px">

        <!-- Left: Guest Management -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <!-- Search Card -->
          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic"><i class="fas fa-search"></i></div>
                بحث عن موظف
              </div>
            </div>
            <div class="hs-card-bd">
              <form method="POST">
                <div class="hs-form-g">
                  <label class="hs-lbl" data-i18n="search">اسم الموظف</label>
                  <div class="hs-search-bar">
                    <i class="hs-search-ic fas fa-search"></i>
                    <input class="hs-search-in" type="text" name="search"
                           placeholder="ابحث عن اسم..."
                           value="<?= htmlspecialchars($search) ?>">
                  </div>
                </div>
                <button type="submit" class="hs-btn hs-btn-primary hs-btn-sm">
                  <i class="fas fa-search"></i> بحث
                </button>
              </form>
            </div>
          </div>

          <!-- Results -->
          <?php if(!empty($search) && $guestResult && $guestResult->num_rows > 0): ?>
            <?php while($guest = $guestResult->fetch_assoc()): ?>
              <div class="hs-card">
                <div class="hs-card-hd">
                  <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--g-400),var(--g-700));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;flex-shrink:0">
                      <?= mb_substr($guest['guest_name'], 0, 1, 'UTF-8') ?>
                    </div>
                    <div>
                      <div style="font-weight:700;color:var(--tx-1)"><?= htmlspecialchars($guest['guest_name']) ?></div>
                      <div style="font-size:.78rem;color:var(--tx-3)"><?= htmlspecialchars($guest['role'] ?? 'موظف') ?></div>
                    </div>
                  </div>
                </div>
                <div class="hs-card-bd">
                  <form method="POST">
                    <input type="hidden" name="guest_id" value="<?= $guest['guest_id'] ?>">
                    <div class="hs-form-g">
                      <label class="hs-lbl" data-i18n="room">الغرفة الحالية</label>
                      <select name="new_room" class="hs-sel">
                        <?php
                        $roomsResult->data_seek(0);
                        while($room = $roomsResult->fetch_assoc()): ?>
                          <option value="<?= $room['room_number'] ?>"
                            <?= ($room['room_number'] == ($guest['room_number']??'')) ? 'selected' : '' ?>>
                            غرفة <?= $room['room_number'] ?>
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap">
                      <button type="submit" name="update_room" class="hs-btn hs-btn-primary hs-btn-sm">
                        <i class="fas fa-save"></i> <span data-i18n="upd_room">تحديث الغرفة</span>
                      </button>
                      <button type="submit" name="remove_guest" class="hs-btn hs-btn-danger hs-btn-sm"
                              onclick="return confirm('هل تريد حذف هذا الموظف؟')">
                        <i class="fas fa-trash-alt"></i> <span data-i18n="rm_guest">حذف الموظف</span>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            <?php endwhile; ?>
          <?php elseif(!empty($search)): ?>
            <div class="hs-card">
              <div class="hs-card-bd" style="text-align:center;padding:40px;color:var(--tx-3)">
                <i class="fas fa-search" style="font-size:2rem;display:block;margin-bottom:12px;opacity:.3"></i>
                <span data-i18n="no_match">لا توجد بيانات مطابقة</span>
              </div>
            </div>
          <?php endif; ?>

        </div><!-- /left -->

        <!-- Right: Add Room -->
        <div>
          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:var(--au-50);color:var(--au-700)"><i class="fas fa-door-open"></i></div>
                <span data-i18n="add_room">إضافة غرفة جديدة</span>
              </div>
            </div>
            <div class="hs-card-bd">
              <p style="color:var(--tx-3);font-size:.875rem;margin-bottom:20px">أضف رقم غرفة جديدة إلى النظام</p>
              <form method="POST">
                <div class="hs-form-g">
                  <label class="hs-lbl hs-lbl-req" data-i18n="room_no">رقم الغرفة</label>
                  <input type="number" name="room_number" class="hs-input"
                         placeholder="مثال: 101" min="1" required>
                </div>
                <button type="submit" name="add_room" class="hs-btn hs-btn-primary hs-btn-block">
                  <i class="fas fa-plus"></i> <span data-i18n="add_room">إضافة الغرفة</span>
                </button>
              </form>

              <!-- Rooms overview -->
              <div class="hs-divider"></div>
              <div style="font-size:.85rem;font-weight:600;color:var(--tx-2);margin-bottom:12px">الغرف المسجلة</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px">
                <?php
                $roomsResult->data_seek(0);
                while($r = $roomsResult->fetch_assoc()):
                ?>
                  <span class="hs-badge hs-badge-g" style="padding:6px 12px"><?= $r['room_number'] ?></span>
                <?php endwhile; ?>
              </div>
            </div>
          </div>
        </div><!-- /right -->

      </div><!-- /grid -->
    </main>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
