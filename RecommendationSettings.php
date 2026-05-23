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
  <style>
    #map {
      height: 480px;
      width: 100%;
      border-radius: var(--r-lg);
      border: 1.5px solid var(--bd-2);
    }
    .coord-display { font-family: monospace; font-size: 1.05rem; }
    .status-badge { font-size: .88rem; padding: 8px 14px; }
    .search-box { position: relative; }
    .search-box .hs-input { padding-right: 44px; }
    body.lang-en .search-box .hs-input { padding-right: 14px; padding-left: 44px; }
    .search-box i {
      position: absolute; top: 50%; right: 14px;
      transform: translateY(-50%);
      color: var(--tx-3); pointer-events: none;
    }
    body.lang-en .search-box i { right: auto; left: 14px; }
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
          <span class="hs-uname">مدير</span>
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
                <h2 style="color:#fff;margin-bottom:4px;font-size:1.15rem">إعدادات موقع الفندق</h2>
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
              حدد موقع الفندق على الخريطة
            </div>
          </div>
          <div class="hs-card-bd">
            <!-- Address Search -->
            <div class="hs-form-g">
              <label class="hs-lbl">بحث بالعنوان</label>
              <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="hs-input"
                       placeholder="ابحث عن عنوان أو مكان...">
              </div>
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
                معلومات الموقع
              </div>
            </div>
            <div class="hs-card-bd">
              <form id="locationForm">

                <div class="hs-form-g">
                  <label class="hs-lbl">خط العرض (Latitude)</label>
                  <input type="text" id="latitude" name="latitude"
                         class="hs-input coord-display" readonly placeholder="0.00000000">
                </div>

                <div class="hs-form-g">
                  <label class="hs-lbl">خط الطول (Longitude)</label>
                  <input type="text" id="longitude" name="longitude"
                         class="hs-input coord-display" readonly placeholder="0.00000000">
                </div>

                <div class="hs-divider"></div>

                <div class="hs-form-g">
                  <label class="hs-lbl">اسم / وصف الموقع (اختياري)</label>
                  <input type="text" id="label" name="label"
                         class="hs-input" placeholder="مثال: فندق العتبة المقدسة">
                </div>

                <!-- Last Updated Info -->
                <div style="padding:14px;background:var(--s-2);border-radius:var(--r-md);margin-bottom:20px;font-size:.82rem;color:var(--tx-2)">
                  <div style="margin-bottom:4px"><strong>آخر تحديث:</strong> <span id="lastUpdated">-</span></div>
                  <div><strong>بواسطة:</strong> <span id="updatedBy">-</span></div>
                </div>

                <button type="submit" class="hs-btn hs-btn-primary hs-btn-block" id="saveBtn">
                  <i class="fas fa-save"></i> حفظ الموقع
                </button>

              </form>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="hs-card">
            <div class="hs-card-hd">
              <div class="hs-card-title">
                <div class="hs-card-ic" style="background:var(--au-50);color:var(--au-700)"><i class="fas fa-bolt"></i></div>
                إجراءات سريعة
              </div>
            </div>
            <div class="hs-card-bd" style="display:flex;flex-direction:column;gap:10px">
              <button class="hs-btn hs-btn-sec hs-btn-block" onclick="getCurrentLocation()">
                <i class="fas fa-crosshairs"></i> استخدم موقعي الحالي
              </button>
              <a href="RecommendationService.php" class="hs-btn hs-btn-ghost hs-btn-block">
                <i class="fas fa-compass"></i> خدمة التوصيات
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
<script>
let map, marker, geocoder, searchBox;
let currentLocation = { lat: 33.3152, lng: 44.3661 }; // Default: Baghdad

// Initialize
document.addEventListener('DOMContentLoaded', async function() {
    await loadMapsAPI();
    loadCurrentLocation();
});

// Load Google Maps API dynamically
async function loadMapsAPI() {
    try {
        const response = await fetch('api/recommendation/maps_config.php');
        const data = await response.json();

        if (!data.success || !data.data.configured) {
            showToast('خطأ', 'مفتاح Google Maps API غير مُعد. يرجى تكوينه في ملف .env', 'danger');
            document.getElementById('map').innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 bg-light" style="height:480px;border-radius:var(--r-lg)">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Google Maps API غير مُعد</h5>
                        <p class="text-muted">يرجى إضافة GOOGLE_MAPS_API_KEY في ملف .env</p>
                    </div>
                </div>
            `;
            return;
        }

        // Load Google Maps script
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${data.data.apiKey}&libraries=places&callback=initMap`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

    } catch (error) {
        console.error('Failed to load Maps API:', error);
        showToast('خطأ', 'فشل في تحميل Google Maps', 'danger');
    }
}

// Initialize map (called by Google Maps callback)
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: currentLocation,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: false
    });

    // Create draggable marker
    marker = new google.maps.Marker({
        position: currentLocation,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'موقع الفندق'
    });

    // Geocoder for address search
    geocoder = new google.maps.Geocoder();

    // Search box
    const input = document.getElementById('searchInput');
    searchBox = new google.maps.places.SearchBox(input);

    // Bias search results to map viewport
    map.addListener('bounds_changed', () => {
        searchBox.setBounds(map.getBounds());
    });

    // Handle search results
    searchBox.addListener('places_changed', () => {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const place = places[0];
        if (!place.geometry || !place.geometry.location) return;

        map.setCenter(place.geometry.location);
        map.setZoom(17);
        marker.setPosition(place.geometry.location);
        updateCoordinates(place.geometry.location);
    });

    // Click on map to move marker
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng);
        updateCoordinates(e.latLng);
    });

    // Drag marker
    marker.addListener('dragend', () => {
        updateCoordinates(marker.getPosition());
    });
}

// Update coordinate inputs
function updateCoordinates(latLng) {
    document.getElementById('latitude').value = latLng.lat().toFixed(8);
    document.getElementById('longitude').value = latLng.lng().toFixed(8);
}

// Load current saved location
async function loadCurrentLocation() {
    try {
        const response = await fetch('api/recommendation/hotel_location.php');
        const data = await response.json();

        if (data.success && data.data) {
            const loc = data.data;

            if (loc.isConfigured) {
                currentLocation = { lat: loc.latitude, lng: loc.longitude };

                document.getElementById('latitude').value = loc.latitude.toFixed(8);
                document.getElementById('longitude').value = loc.longitude.toFixed(8);
                document.getElementById('label').value = loc.label || '';
                document.getElementById('lastUpdated').textContent = loc.updatedAt || '-';
                document.getElementById('updatedBy').textContent = loc.updatedBy || '-';

                // Update map if already initialized
                if (map && marker) {
                    map.setCenter(currentLocation);
                    marker.setPosition(currentLocation);
                }

                updateStatusBadge(true);
            } else {
                updateStatusBadge(false);
            }
        }
    } catch (error) {
        console.error('Failed to load location:', error);
    }
}

// Update status badge
function updateStatusBadge(isConfigured) {
    const badge = document.getElementById('statusBadge');
    if (isConfigured) {
        badge.innerHTML = `
            <span class="badge bg-success status-badge">
                <i class="fas fa-check-circle me-1"></i> الموقع مُعد
            </span>
        `;
    } else {
        badge.innerHTML = `
            <span class="badge bg-warning status-badge">
                <i class="fas fa-exclamation-triangle me-1"></i> غير مُعد
            </span>
        `;
    }
}

// Get current GPS location
function getCurrentLocation() {
    if (!navigator.geolocation) {
        showToast('خطأ', 'المتصفح لا يدعم تحديد الموقع', 'warning');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            if (map && marker) {
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
            }

            updateCoordinates(new google.maps.LatLng(pos.lat, pos.lng));
            showToast('نجاح', 'تم تحديد موقعك الحالي', 'success');
        },
        (error) => {
            showToast('خطأ', 'فشل في تحديد الموقع: ' + error.message, 'danger');
        }
    );
}

// Save location
document.getElementById('locationForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    const label = document.getElementById('label').value.trim();

    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
        showToast('خطأ', 'يرجى تحديد موقع صالح على الخريطة', 'warning');
        return;
    }

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';

    try {
        const response = await fetch('api/recommendation/hotel_location.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                label: label,
                updatedBy: 'Admin'
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast('نجاح', 'تم حفظ موقع الفندق بنجاح', 'success');
            updateStatusBadge(true);
            document.getElementById('lastUpdated').textContent = new Date().toLocaleString('ar-IQ');
            document.getElementById('updatedBy').textContent = 'Admin';
        } else {
            showToast('خطأ', data.error || 'فشل في حفظ الموقع', 'danger');
        }
    } catch (error) {
        console.error('Save failed:', error);
        showToast('خطأ', 'حدث خطأ في الاتصال', 'danger');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> حفظ الموقع';
    }
});

// Show toast notification
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    toast.className = 'toast';
    if (type === 'success') toast.classList.add('border-success');
    else if (type === 'danger') toast.classList.add('border-danger');
    else if (type === 'warning') toast.classList.add('border-warning');

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>

</body>
</html>
