<?php
/**
 * Admin Settings Page - Hotel Location Configuration
 * Allows admin to set/update hotel location for recommendations
 */
session_start();
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إعدادات التوصيات — الفندق</title>
  <?php include 'includes/head.php'; ?>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <style>
    #map {
      height: 480px;
      width: 100%;
      border-radius: var(--r-lg);
      border: 1.5px solid var(--bd-2);
      z-index: 0;
    }
    .coord-display { font-family: monospace; font-size: 1.05rem; }
    .status-badge { font-size: .88rem; padding: 8px 14px; }
    .leaflet-attribution-flag { display: none !important; }
  </style>
</head>
<body>
<div class="hs-app">
  <?php include 'includes/sidebar.php'; ?>

  <div class="hs-main" id="mainContent">
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title" data-i18n="set_title">إعدادات التوصيات</div>
          <div class="hs-pg-sub" data-i18n="set_sub">تحديد موقع الفندق لخدمة التوصيات</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <div id="statusBadge">
          <span class="badge bg-secondary status-badge">
            <i class="fas fa-spinner fa-spin me-1"></i> جاري التحميل...
          </span>
        </div>
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill">
          <div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div>
          <span class="hs-uname" data-i18n="manager">مدير</span>
        </div>
      </div>
    </header>

    <main class="hs-content hs-stagger">

      <!-- Hero -->
      <div class="hs-card hs-mb-6" style="background:linear-gradient(135deg,var(--g-800),var(--g-600));color:#fff;border:none">
        <div class="hs-card-bd">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:16px">
              <div style="width:52px;height:52px;border-radius:var(--r-xl);background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <h2 style="color:#fff;margin-bottom:4px;font-size:1.15rem" data-i18n="set_hero_title">إعدادات موقع الفندق</h2>
                <p style="color:rgba(255,255,255,.65);margin:0;font-size:.85rem">Hotel Location Settings — Recommendation Service</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1.7fr 1fr;gap:28px;align-items:start">

        <!-- Map Column -->
        <div class="hs-card">
          <div class="hs-card-hd">
            <div class="hs-card-title">
              <div class="hs-card-ic"><i class="fas fa-map"></i></div>
              <span data-i18n="map_select_title">حدد موقع الفندق على الخريطة</span>
            </div>
          </div>
          <div class="hs-card-bd">
            <!-- Address Search -->
            <div class="hs-form-g">
              <label class="hs-lbl" data-i18n="address_search">بحث بالعنوان</label>
              <div style="display:flex;gap:8px">
                <input type="text" id="searchInput" class="hs-input" style="flex:1"
                       data-i18n="address_ph" data-i18n-attr="placeholder"
                       placeholder="ابحث عن عنوان أو مكان...">
                <button type="button" class="hs-btn hs-btn-primary hs-btn-sm" id="searchAddrBtn" style="flex-shrink:0">
                  <i class="fas fa-search"></i>
                </button>
              </div>
              <div id="nominatimResults" style="display:none;margin-top:6px;border:1px solid var(--bd-2);border-radius:var(--r-md);background:var(--s-0);max-height:200px;overflow-y:auto;box-shadow:var(--sh-md);z-index:1000;position:relative"></div>
            </div>

            <!-- Map -->
            <div id="map"></div>

            <!-- Instructions -->
            <div style="margin-top:16px;padding:14px;background:var(--g-25);border-radius:var(--r-md);border-right:3px solid var(--g-500)">
              <div style="font-size:.85rem;font-weight:600;color:var(--g-700);margin-bottom:6px"><i class="fas fa-info-circle me-1"></i> تعليمات:</div>
              <ul style="padding-right:16px;color:var(--tx-2);font-size:.82rem;line-height:2;margin:0">
                <li>اسحب العلامة لتحديد موقع الفندق بدقة</li>
                <li>أو استخدم مربع البحث للعثور على العنوان</li>
                <li>انقر على الخريطة لنقل العلامة</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Settings Column -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <!-- Location Form -->
          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic"><i class="fas fa-cog"></i></div>
                <span data-i18n="location_info">معلومات الموقع</span>
              </div>
            </div>
            <div class="hs-card-bd">
              <form id="locationForm">

                <div class="hs-form-g">
                  <label class="hs-lbl" data-i18n="latitude_label">خط العرض (Latitude)</label>
                  <input type="text" id="latitude" name="latitude"
                         class="hs-input coord-display" readonly placeholder="0.00000000">
                </div>

                <div class="hs-form-g">
                  <label class="hs-lbl" data-i18n="longitude_label">خط الطول (Longitude)</label>
                  <input type="text" id="longitude" name="longitude"
                         class="hs-input coord-display" readonly placeholder="0.00000000">
                </div>

                <div class="hs-divider"></div>

                <div class="hs-form-g">
                  <label class="hs-lbl" data-i18n="location_label_opt">اسم / وصف الموقع (اختياري)</label>
                  <input type="text" id="label" name="label"
                         class="hs-input" data-i18n="location_label_ph" data-i18n-attr="placeholder"
                         placeholder="مثال: فندق العتبة المقدسة">
                </div>

                <!-- Last Updated Info -->
                <div style="padding:14px;background:var(--s-2);border-radius:var(--r-md);margin-bottom:20px;font-size:.82rem;color:var(--tx-2)">
                  <div style="margin-bottom:4px"><strong data-i18n="last_updated">آخر تحديث:</strong> <span id="lastUpdated">-</span></div>
                  <div><strong data-i18n="updated_by">بواسطة:</strong> <span id="updatedBy">-</span></div>
                </div>

                <button type="submit" class="hs-btn hs-btn-primary hs-btn-block" id="saveBtn">
                  <i class="fas fa-save"></i> <span data-i18n="set_save">حفظ الموقع</span>
                </button>

              </form>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:var(--au-50);color:var(--au-700)"><i class="fas fa-bolt"></i></div>
                <span data-i18n="quick_actions">إجراءات سريعة</span>
              </div>
            </div>
            <div class="hs-card-bd" style="display:flex;flex-direction:column;gap:10px">
              <button class="hs-btn hs-btn-sec hs-btn-block" onclick="getCurrentLocation()">
                <i class="fas fa-crosshairs"></i> <span data-i18n="set_cur">استخدم موقعي الحالي</span>
              </button>
              <a href="RecommendationService.php" class="hs-btn hs-btn-ghost hs-btn-block">
                <i class="fas fa-compass"></i> <span data-i18n="rec_service">خدمة التوصيات</span>
              </a>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toast" class="toast" role="alert">
    <div class="toast-header">
      <i class="fas fa-bell me-2 text-success"></i>
      <strong class="me-auto" id="toastTitle">إشعار</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toastMessage"></div>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker;
let currentLocation = { lat: 33.3152, lng: 44.3661 }; // Default: Baghdad

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    loadCurrentLocation();
    setupSearch();

    document.getElementById('locationForm').addEventListener('submit', saveLocation);
});

function initMap() {
    map = L.map('map').setView([currentLocation.lat, currentLocation.lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    const hotelIcon = L.divIcon({
        className: '',
        html: `<div style="background:linear-gradient(135deg,var(--g-600),var(--g-800));color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;border:2px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.35)"><i class="fas fa-hotel"></i></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    marker = L.marker([currentLocation.lat, currentLocation.lng], {
        draggable: true,
        icon: hotelIcon
    }).addTo(map);

    marker.on('dragend', function() {
        const p = marker.getLatLng();
        updateCoordinates(p.lat, p.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value  = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
}

// ── Nominatim address search ──────────────────────────────────────────────
function setupSearch() {
    const btn   = document.getElementById('searchAddrBtn');
    const input = document.getElementById('searchInput');

    btn.addEventListener('click', () => geocodeAddress());
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); geocodeAddress(); }
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#nominatimResults') && !e.target.closest('#searchInput')) {
            document.getElementById('nominatimResults').style.display = 'none';
        }
    });
}

async function geocodeAddress() {
    const q = document.getElementById('searchInput').value.trim();
    if (!q) return;

    const resultsBox = document.getElementById('nominatimResults');
    resultsBox.innerHTML = '<div style="padding:10px;color:var(--tx-3);font-size:.85rem">جاري البحث...</div>';
    resultsBox.style.display = 'block';

    try {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5&accept-language=ar,en`;
        const resp = await fetch(url);
        const data = await resp.json();

        if (!data.length) {
            resultsBox.innerHTML = '<div style="padding:10px;color:var(--tx-3);font-size:.85rem">لم يتم العثور على نتائج</div>';
            return;
        }

        resultsBox.innerHTML = data.map((item, i) => `
            <div onclick="selectAddress(${parseFloat(item.lat)}, ${parseFloat(item.lon)}, '${item.display_name.replace(/'/g,"&#39;")}')"
                 style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--bd-1);font-size:.85rem;transition:var(--tf)"
                 onmouseover="this.style.background='var(--g-25)'" onmouseout="this.style.background=''">
                <i class="fas fa-map-marker-alt" style="color:var(--g-600);margin-left:6px"></i>
                ${item.display_name}
            </div>
        `).join('');
    } catch (err) {
        resultsBox.innerHTML = '<div style="padding:10px;color:#DC2626;font-size:.85rem">فشل في الاتصال بخدمة البحث</div>';
    }
}

function selectAddress(lat, lng, displayName) {
    document.getElementById('nominatimResults').style.display = 'none';
    document.getElementById('searchInput').value = displayName;

    map.setView([lat, lng], 17);
    marker.setLatLng([lat, lng]);
    updateCoordinates(lat, lng);
}

// ── Load saved location ───────────────────────────────────────────────────
async function loadCurrentLocation() {
    try {
        const resp = await fetch('api/recommendation/hotel_location.php');
        const data = await resp.json();

        if (data.success && data.data && data.data.isConfigured) {
            const loc = data.data;
            currentLocation = { lat: loc.latitude, lng: loc.longitude };

            document.getElementById('latitude').value  = loc.latitude.toFixed(8);
            document.getElementById('longitude').value = loc.longitude.toFixed(8);
            document.getElementById('label').value     = loc.label || '';
            document.getElementById('lastUpdated').textContent = loc.updatedAt || '-';
            document.getElementById('updatedBy').textContent   = loc.updatedBy || '-';

            map.setView([loc.latitude, loc.longitude], 15);
            marker.setLatLng([loc.latitude, loc.longitude]);
            updateStatusBadge(true);
        } else {
            updateStatusBadge(false);
        }
    } catch (err) {
        console.error('loadCurrentLocation:', err);
    }
}

// ── GPS ───────────────────────────────────────────────────────────────────
function getCurrentLocation() {
    if (!navigator.geolocation) {
        showToast('خطأ', 'المتصفح لا يدعم تحديد الموقع', 'warning');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            map.setView([lat, lng], 17);
            marker.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
            showToast('نجاح', 'تم تحديد موقعك الحالي', 'success');
        },
        err => showToast('خطأ', 'فشل في تحديد الموقع: ' + err.message, 'danger')
    );
}

// ── Save ──────────────────────────────────────────────────────────────────
async function saveLocation(e) {
    e.preventDefault();
    const lat   = parseFloat(document.getElementById('latitude').value);
    const lng   = parseFloat(document.getElementById('longitude').value);
    const label = document.getElementById('label').value.trim();

    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
        showToast('خطأ', 'يرجى تحديد موقع على الخريطة أولاً', 'warning');
        return;
    }

    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';

    try {
        const resp = await fetch('api/recommendation/hotel_location.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ latitude: lat, longitude: lng, label, updatedBy: 'Admin' })
        });
        const data = await resp.json();

        if (data.success) {
            showToast('نجاح', 'تم حفظ موقع الفندق بنجاح', 'success');
            updateStatusBadge(true);
            document.getElementById('lastUpdated').textContent = new Date().toLocaleString('ar-IQ');
            document.getElementById('updatedBy').textContent   = 'Admin';
        } else {
            showToast('خطأ', data.error || 'فشل في حفظ الموقع', 'danger');
        }
    } catch (err) {
        showToast('خطأ', 'حدث خطأ في الاتصال', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <span data-i18n="set_save">حفظ الموقع</span>';
        Lang.apply();
    }
}

// ── Status badge ──────────────────────────────────────────────────────────
function updateStatusBadge(ok) {
    document.getElementById('statusBadge').innerHTML = ok
        ? `<span class="badge bg-success status-badge"><i class="fas fa-check-circle me-1"></i> الموقع مُعد</span>`
        : `<span class="badge bg-warning status-badge"><i class="fas fa-exclamation-triangle me-1"></i> غير مُعد</span>`;
}

// ── Toast ─────────────────────────────────────────────────────────────────
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    document.getElementById('toastTitle').textContent   = title;
    document.getElementById('toastMessage').textContent = message;
    toast.className = 'toast';
    if (type === 'success') toast.classList.add('border-success');
    else if (type === 'danger')  toast.classList.add('border-danger');
    else if (type === 'warning') toast.classList.add('border-warning');
    new bootstrap.Toast(toast).show();
}
</script>

</body>
</html>
