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
    <title>إعدادات خدمة التوصيات - Hotel Recommendation Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 10px;
            border: 2px solid #dee2e6;
        }
        .location-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
        }
        .btn-save:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            padding-right: 45px;
        }
        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .coord-display {
            font-family: monospace;
            font-size: 1.1rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
        .instructions {
            background-color: #f8f9fa;
            border-right: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success navbar-light">
    <div class="container-xxl">
        <a class="navbar-brand" href="index.php">
            <img src="assets/logo/Full_logo.png" alt="Logo" width="auto" height="30">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse justify-content-end pe-3" id="main-nav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link text-white" href="RecommendationService.php">خدمة التوصيات</a></li>
            <li class="nav-item"><a class="nav-link text-white active" href="RecommendationSettings.php">إعدادات التوصيات</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئيسية</a></li>
        </ul>
    </div>
</nav>

<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card location-card shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>إعدادات موقع الفندق</h2>
                            <p class="mb-0 opacity-75">Hotel Location Settings - Recommendation Service</p>
                        </div>
                        <div id="statusBadge">
                            <span class="badge bg-secondary status-badge">
                                <i class="fas fa-spinner fa-spin me-1"></i> جاري التحميل...
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map me-2 text-primary"></i>حدد موقع الفندق على الخريطة</h5>
                </div>
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="search-box mb-3">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control form-control-lg" 
                               placeholder="ابحث عن عنوان أو مكان...">
                    </div>
                    
                    <!-- Map Container -->
                    <div id="map"></div>
                    
                    <div class="instructions mt-3">
                        <h6><i class="fas fa-info-circle text-primary me-2"></i>تعليمات:</h6>
                        <ul class="mb-0 small">
                            <li>اسحب العلامة لتحديد موقع الفندق بدقة</li>
                            <li>أو استخدم مربع البحث للعثور على العنوان</li>
                            <li>انقر على الخريطة لنقل العلامة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Column -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>معلومات الموقع</h5>
                </div>
                <div class="card-body">
                    <form id="locationForm">
                        <!-- Coordinates Display -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">خط العرض (Latitude)</label>
                            <input type="text" id="latitude" name="latitude" class="form-control coord-display" 
                                   readonly placeholder="0.00000000">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">خط الطول (Longitude)</label>
                            <input type="text" id="longitude" name="longitude" class="form-control coord-display" 
                                   readonly placeholder="0.00000000">
                        </div>
                        
                        <hr>
                        
                        <!-- Label -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم/وصف الموقع (اختياري)</label>
                            <input type="text" id="label" name="label" class="form-control" 
                                   placeholder="مثال: فندق العتبة المقدسة">
                        </div>
                        
                        <!-- Last Updated Info -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <div><strong>آخر تحديث:</strong> <span id="lastUpdated">-</span></div>
                                <div><strong>بواسطة:</strong> <span id="updatedBy">-</span></div>
                            </small>
                        </div>
                        
                        <!-- Save Button -->
                        <button type="submit" class="btn btn-save btn-lg w-100 text-white" id="saveBtn">
                            <i class="fas fa-save me-2"></i>حفظ الموقع
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-bolt me-2 text-warning"></i>إجراءات سريعة</h6>
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="getCurrentLocation()">
                        <i class="fas fa-crosshairs me-2"></i>استخدم موقعي الحالي
                    </button>
                    <a href="RecommendationService.php" class="btn btn-outline-success w-100">
                        <i class="fas fa-compass me-2"></i>جرب خدمة التوصيات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast for notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fas fa-bell me-2 text-primary"></i>
            <strong class="me-auto" id="toastTitle">إشعار</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

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
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
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
        saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>حفظ الموقع';
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
