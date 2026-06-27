<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'connect.php';

$guest_logs = [];
$guest_query = $conn->prepare("SELECT cl.log_id, cl.guest_id, g.guest_name
    FROM check_logs cl JOIN guests g ON cl.guest_id = g.guest_id
    WHERE cl.return_time IS NULL");
$guest_query->execute();
$guest_result = $guest_query->get_result();
$arr_of_guest = $guest_result->fetch_all(MYSQLI_ASSOC);

$search = '';
$search_results = $arr_of_guest;
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $search = $conn->real_escape_string(trim($_POST['search']));
    $sq = "SELECT cl.log_id, cl.guest_id, g.guest_name
           FROM check_logs cl JOIN guests g ON cl.guest_id=g.guest_id
           WHERE cl.return_time IS NULL AND g.guest_name LIKE '%$search%'";
    $sr = $conn->query($sq);
    if($sr) $search_results = $sr->fetch_all(MYSQLI_ASSOC);
    else $search_results = [];
}
$arr_of_guest = $search_results;
?>

<!-- Pending Check-In Card -->
<div class="hs-card">
  <div class="hs-card-hd">
    <div class="hs-card-title">
      <div class="hs-card-ic" style="background:var(--g-50);color:var(--g-700)"><i class="fas fa-sign-out-alt"></i></div>
      <span data-i18n="co_title">تسجيل المغادرة</span>
    </div>
    <span class="hs-badge hs-badge-<?= count($arr_of_guest) > 0 ? 'au' : 'gray' ?>">
      <?= count($arr_of_guest) ?> <span data-i18n="pending">في الانتظار</span>
    </span>
  </div>

  <!-- Search -->
  <div style="padding:16px 24px;border-bottom:1px solid var(--bd-1)">
    <form method="POST" action="CheckIn.php">
      <div class="hs-search-bar" style="max-width:440px">
        <i class="hs-search-ic fas fa-search"></i>
        <input class="hs-search-in" type="text" name="search"
               data-i18n="search_name_ph" data-i18n-attr="placeholder"
               placeholder="ابحث عن اسم..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <button type="submit" class="hs-btn hs-btn-primary hs-btn-sm" style="margin-top:10px">
        <i class="fas fa-search"></i> <span data-i18n="search">بحث</span>
      </button>
    </form>
  </div>

  <!-- List -->
  <div class="hs-card-bd" style="padding:0">
    <?php if(empty($arr_of_guest)): ?>
      <div style="text-align:center;padding:48px 24px;color:var(--tx-3)">
        <i class="fas fa-check-circle" style="font-size:3rem;margin-bottom:16px;display:block;color:var(--g-200)"></i>
        <p style="font-weight:600" data-i18n="no_data">لا توجد بيانات للعرض</p>
        <p style="font-size:.85rem" data-i18n="all_present">جميع الموظفين موجودون</p>
      </div>
    <?php else: ?>
      <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
        <?php foreach($arr_of_guest as $log): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;background:var(--au-50);border-radius:var(--r-lg);border:1px solid var(--au-100)">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--au-300),var(--au-500));display:flex;align-items:center;justify-content:center;color:var(--tx-1);font-weight:700;font-size:.85rem;flex-shrink:0">
                <?= mb_substr($log['guest_name'], 0, 1, 'UTF-8') ?>
              </div>
              <div>
                <div style="font-weight:600;color:var(--tx-1)"><?= htmlspecialchars($log['guest_name']) ?></div>
                <div style="font-size:.78rem;color:var(--au-700);display:flex;align-items:center;gap:4px">
                  <span class="hs-status-dot hs-dot-au hs-pulse"></span> <span data-i18n="outside">خارج المقر</span>
                </div>
              </div>
            </div>
            <button type="button" class="hs-btn hs-btn-gold hs-btn-sm"
                    onclick="checkIn(<?= $log['log_id'] ?>)">
              <i class="fas fa-check"></i> <span data-i18n="co_btn">تأكيد المغادرة</span>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
