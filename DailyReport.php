<?php
include 'connect.php';

$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');
$search = isset($_POST['search']) ? $_POST['search'] : '';

$sql = "SELECT cl.log_id, cl.checkout_time, cl.return_time, g.guest_name
        FROM check_logs cl JOIN guests g ON cl.guest_id = g.guest_id
        WHERE DATE(cl.checkout_time) = '$selectedDate'
        AND (g.guest_name LIKE '%$search%')";
$result = $conn->query($sql);
$totalRows = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <title>التقرير اليومي — الفندق</title>
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
          <div class="hs-pg-title" data-i18n="rpt_title">التقرير اليومي</div>
          <div class="hs-pg-sub"><?= date('d/m/Y', strtotime($selectedDate)) ?></div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <form method="POST" action="export_excel.php?selected_date=<?= $selectedDate ?>" style="margin:0">
          <button type="submit" class="hs-btn hs-btn-gold hs-btn-sm">
            <i class="fas fa-file-excel"></i> <span data-i18n="export_xl">تصدير Excel</span>
          </button>
        </form>
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill"><div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div><span class="hs-uname">موظف</span></div>
      </div>
    </header>

    <main class="hs-content hs-stagger">

      <!-- Stats Row -->
      <div class="hs-g3 hs-mb-6">
        <div class="hs-stat-card">
          <div class="hs-stat-ic hs-stat-ic-g"><i class="fas fa-users"></i></div>
          <div class="hs-stat-val"><?= $totalRows ?></div>
          <div class="hs-stat-lbl">إجمالي السجلات</div>
        </div>
        <div class="hs-stat-card">
          <div class="hs-stat-ic hs-stat-ic-au"><i class="fas fa-calendar-day"></i></div>
          <div class="hs-stat-val"><?= date('d', strtotime($selectedDate)) ?></div>
          <div class="hs-stat-lbl"><?= date('F Y', strtotime($selectedDate)) ?></div>
        </div>
        <div class="hs-stat-card">
          <div class="hs-stat-ic hs-stat-ic-b"><i class="fas fa-clock"></i></div>
          <div class="hs-stat-val"><?= date('H:i') ?></div>
          <div class="hs-stat-lbl">الوقت الحالي</div>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="hs-card hs-mb-6">
        <div class="hs-card-hd">
          <div class="hs-card-title">
            <div class="hs-card-ic"><i class="fas fa-filter"></i></div>
            فلترة التقرير
          </div>
        </div>
        <div class="hs-card-bd">
          <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end">
            <!-- Date Picker -->
            <div style="flex:1;min-width:200px">
              <label class="hs-lbl" data-i18n="sel_date">اختر التاريخ</label>
              <div style="position:relative">
                <i class="fas fa-calendar" style="position:absolute;top:50%;right:13px;transform:translateY(-50%);color:var(--tx-3);font-size:14px;pointer-events:none"></i>
                <input type="date" id="datePicker" class="hs-input" value="<?= $selectedDate ?>" style="padding-right:38px;cursor:pointer">
              </div>
            </div>
            <!-- Search -->
            <form method="POST" style="flex:2;min-width:240px">
              <label class="hs-lbl" data-i18n="search">بحث بالاسم</label>
              <div class="hs-search-bar">
                <i class="hs-search-ic fas fa-search"></i>
                <input class="hs-search-in" type="text" name="search"
                       placeholder="ابحث عن اسم الموظف..."
                       value="<?= htmlspecialchars($search) ?>">
              </div>
            </form>
            <form method="POST" style="align-self:flex-end">
              <button type="submit" class="hs-btn hs-btn-primary">
                <i class="fas fa-search"></i> بحث
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="hs-card">
        <div class="hs-card-hd">
          <div class="hs-card-title">
            <div class="hs-card-ic"><i class="fas fa-table"></i></div>
            سجلات اليوم
          </div>
          <span class="hs-badge hs-badge-g"><?= $totalRows ?> سجل</span>
        </div>
        <div class="hs-tbl-wrap" style="border:none;border-radius:0 0 var(--r-xl) var(--r-xl)">
          <table class="hs-tbl">
            <thead>
              <tr>
                <th data-i18n="col_name">الاسم</th>
                <th data-i18n="col_ci">وقت المغادرة</th>
                <th data-i18n="col_co">وقت العودة</th>
                <th>الحالة</th>
                <th data-i18n="col_del">حذف</th>
              </tr>
            </thead>
            <tbody>
              <?php if($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--g-400),var(--g-600));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0">
                          <?= mb_substr($row['guest_name'], 0, 1, 'UTF-8') ?>
                        </div>
                        <span style="font-weight:600"><?= htmlspecialchars($row['guest_name']) ?></span>
                      </div>
                    </td>
                    <td>
                      <span style="font-family:monospace;background:var(--g-50);color:var(--g-700);padding:4px 10px;border-radius:var(--r-full);font-size:.85rem;font-weight:600">
                        <?= date('H:i', strtotime($row['checkout_time'])) ?>
                      </span>
                    </td>
                    <td>
                      <?php if($row['return_time']): ?>
                        <span style="font-family:monospace;background:var(--au-50);color:var(--au-700);padding:4px 10px;border-radius:var(--r-full);font-size:.85rem;font-weight:600">
                          <?= date('H:i', strtotime($row['return_time'])) ?>
                        </span>
                      <?php else: ?>
                        <span class="hs-badge hs-badge-au"><span class="hs-status-dot hs-dot-au hs-pulse"></span> خارج</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($row['return_time']): ?>
                        <span class="hs-badge hs-badge-g"><i class="fas fa-check"></i> عاد</span>
                      <?php else: ?>
                        <span class="hs-badge hs-badge-au">غائب</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <form method="POST" action="delete_log.php" style="display:inline" onsubmit="return confirm('هل تريد حذف هذا السجل؟')">
                        <input type="hidden" name="log_id" value="<?= $row['log_id'] ?>">
                        <button type="submit" class="hs-btn hs-btn-danger hs-btn-ic hs-btn-sm" title="حذف">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align:center;padding:48px;color:var(--tx-3)">
                    <i class="fas fa-inbox" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.3"></i>
                    <span data-i18n="no_data">لا توجد بيانات للعرض</span>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('datePicker').addEventListener('change', function() {
  window.location.href = `DailyReport.php?selected_date=${this.value}`;
});
</script>
</body>
</html>
<?php $conn->close(); ?>
