<?php
/**
 * Recommendation Service Page
 * Staff can search nearby places for guests
 */
session_start();
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خدمة التوصيات - Hotel Recommendations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .hero-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px;
        }
        
        .category-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .category-card.selected {
            border-color: #667eea;
            background-color: #f0f4ff;
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 1.5rem;
            margin: 0 auto 10px;
        }
        
        .search-form-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
        }
        
        .btn-search {
            background: var(--primary-gradient);
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 10px;
        }
        
        .btn-search:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        }
        
        .btn-search:disabled {
            background: #ccc;
        }
        
        .guest-input-group {
            position: relative;
        }
        
        .guest-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: none;
        }
        
        .guest-suggestion-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .guest-suggestion-item:hover {
            background-color: #f8f9fa;
        }
        
        .guest-suggestion-item:last-child {
            border-bottom: none;
        }
        
        .selected-guest {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
        
        /* Results Modal Styles */
        .results-modal .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .results-modal .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .results-map {
            height: 350px;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .place-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 15px;
        }
        
        .place-card:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        
        .place-card.selected {
            border-color: #28a745;
            background-color: #e8f5e9;
        }
        
        .place-number {
            width: 35px;
            height: 35px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .place-rating {
            color: #ffc107;
        }
        
        .open-badge {
            font-size: 0.75rem;
        }
        
        .directions-card {
            background: var(--success-gradient);
            color: white;
            border-radius: 15px;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }
        
        .loading-spinner {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
        }
        
        .optional-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: normal;
        }
        
        .nationality-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #7c4a03;
        }
        
        .boosted-badge {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #0d6efd;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
        <h5>جاري البحث...</h5>
        <p class="text-muted mb-0">يرجى الانتظار</p>
    </div>
</div>

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
            <li class="nav-item"><a class="nav-link text-white active" href="RecommendationService.php">خدمة التوصيات</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="RecommendationSettings.php">إعدادات التوصيات</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckIn.php">تسجيل دخول</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="CheckOut.php">تسجيل خروج</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php">الرئيسية</a></li>
        </ul>
    </div>
</nav>

<div class="container py-4">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card hero-card shadow p-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <i class="fas fa-compass fa-3x"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">خدمة توصيات الأماكن القريبة</h2>
                        <p class="mb-0 opacity-75">Hotel Nearby Recommendations - ابحث عن أفضل الأماكن للضيوف</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card search-form-card p-4">
                <form id="searchForm">
                    <!-- Guest Input (Optional) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2 text-primary"></i>
                                اسم الضيف أو رقم الجواز
                                <span class="optional-label">(اختياري - يُستخدم لتخصيص النتائج حسب الجنسية)</span>
                            </label>
                            <div class="guest-input-group">
                                <input type="text" id="guestInput" class="form-control form-control-lg" 
                                       placeholder="ابحث عن ضيف بالاسم أو رقم الجواز..."
                                       autocomplete="off">
                                <div class="guest-suggestions" id="guestSuggestions"></div>
                            </div>
                            
                            <!-- Selected Guest Display -->
                            <div class="selected-guest" id="selectedGuestCard" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong id="selectedGuestName"></strong>
                                        <span class="nationality-badge ms-2" id="selectedGuestNationality"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedGuest()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-id-card me-1"></i>
                                    <span id="selectedGuestPassport"></span>
                                    &nbsp;|&nbsp;
                                    <i class="fas fa-door-open me-1"></i>
                                    غرفة <span id="selectedGuestRoom"></span>
                                </small>
                                <input type="hidden" id="selectedGuestId" name="guestId">
                            </div>
                        </div>
                    </div>

                    <!-- Category Selection (Required) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-th-large me-2 text-primary"></i>
                            اختر الفئة
                            <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3" id="categoriesGrid">
                            <!-- Categories will be loaded here -->
                        </div>
                        <input type="hidden" id="selectedCategory" name="category" required>
                    </div>

                    <!-- Search Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-search btn-lg text-white" id="searchBtn" disabled>
                            <i class="fas fa-search me-2"></i>
                            بحث عن الأماكن القريبة
                        </button>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            يجب اختيار فئة للبحث. بيانات الضيف اختيارية.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade results-modal" id="resultsModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    نتائج البحث - <span id="resultsCategoryName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search Info -->
                <div class="alert alert-info mb-4" id="searchInfoAlert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>معلومات البحث</strong>
                            <p class="mb-0 small" id="searchInfoText"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Map Column -->
                    <div class="col-lg-6 mb-4">
                        <div class="results-map" id="resultsMap"></div>
                    </div>

                    <!-- Places List Column -->
                    <div class="col-lg-6">
                        <h6 class="mb-3">
                            <i class="fas fa-list-ol me-2"></i>
                            الأماكن القريبة (<span id="placesCount">0</span>)
                        </h6>
                        <div id="placesList"></div>
                    </div>
                </div>

                <!-- Selected Place Directions -->
                <div class="directions-card p-4 mt-4" id="directionsCard" style="display: none;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5><i class="fas fa-directions me-2"></i>الاتجاهات إلى</h5>
                            <h4 id="selectedPlaceName" class="mb-0"></h4>
                            <small id="selectedPlaceDistance"></small>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <a href="#" id="openMapsBtn" target="_blank" class="btn btn-light btn-lg me-2">
                                <i class="fab fa-google me-2"></i>
                                فتح في Google Maps
                            </a>
                            <button type="button" class="btn btn-outline-light" id="copyLinkBtn">
                                <i class="fas fa-copy me-2"></i>
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Guest Selection Modal (for multiple matches) -->
<div class="modal fade" id="guestSelectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2"></i>اختر الضيف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">تم العثور على عدة ضيوف. يرجى اختيار الضيف المطلوب:</p>
                <div id="guestSelectList"></div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary" onclick="continueWithoutGuest()">
                        متابعة بدون ضيف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto" id="toastTitle">إشعار</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// State
let selectedGuest = null;
let selectedCategory = null;
let searchResults = null;
let resultsMap = null;
let resultsMarkers = [];
let hotelMarker = null;
let selectedPlace = null;
let mapsApiKey = null;
let debounceTimer = null;

// Category icons and colors
const categoryStyles = {
    restaurants: { icon: 'fa-utensils', bg: '#e74c3c' },
    cafes: { icon: 'fa-coffee', bg: '#8e44ad' },
    clothing: { icon: 'fa-tshirt', bg: '#3498db' },
    playgrounds: { icon: 'fa-child', bg: '#27ae60' },
    pharmacies: { icon: 'fa-pills', bg: '#1abc9c' },
    supermarkets: { icon: 'fa-shopping-cart', bg: '#f39c12' },
    malls: { icon: 'fa-building', bg: '#9b59b6' },
    museums: { icon: 'fa-landmark', bg: '#34495e' },
    parks: { icon: 'fa-tree', bg: '#2ecc71' },
    hospitals: { icon: 'fa-hospital', bg: '#e74c3c' },
    banks: { icon: 'fa-university', bg: '#2c3e50' },
    gas_stations: { icon: 'fa-gas-pump', bg: '#f1c40f' }
};

// Initialize
document.addEventListener('DOMContentLoaded', async function() {
    await loadMapsApiKey();
    loadCategories();
    setupGuestSearch();
    setupForm();
});

// Load Maps API Key
async function loadMapsApiKey() {
    try {
        const response = await fetch('api/recommendation/maps_config.php');
        const data = await response.json();
        if (data.success && data.data.apiKey) {
            mapsApiKey = data.data.apiKey;
        }
    } catch (error) {
        console.error('Failed to load maps API key:', error);
    }
}

// Load categories
async function loadCategories() {
    try {
        const response = await fetch('api/recommendation/categories.php');
        const data = await response.json();
        
        if (data.success) {
            renderCategories(data.data);
        }
    } catch (error) {
        console.error('Failed to load categories:', error);
        showToast('خطأ', 'فشل في تحميل الفئات', 'danger');
    }
}

// Render category cards
function renderCategories(categories) {
    const grid = document.getElementById('categoriesGrid');
    grid.innerHTML = categories.map(cat => {
        const style = categoryStyles[cat.value] || { icon: 'fa-map-marker', bg: '#667eea' };
        return `
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card category-card text-center p-3" data-category="${cat.value}">
                    <div class="category-icon" style="background: ${style.bg}; color: white;">
                        <i class="fas ${style.icon}"></i>
                    </div>
                    <h6 class="mb-0">${cat.label}</h6>
                    <small class="text-muted">${cat.label_en}</small>
                </div>
            </div>
        `;
    }).join('');
    
    // Add click handlers
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', () => selectCategory(card.dataset.category));
    });
}

// Select category
function selectCategory(category) {
    selectedCategory = category;
    document.getElementById('selectedCategory').value = category;
    
    // Update UI
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.toggle('selected', card.dataset.category === category);
    });
    
    // Enable search button
    document.getElementById('searchBtn').disabled = false;
}

// Setup guest search autocomplete
function setupGuestSearch() {
    const input = document.getElementById('guestInput');
    const suggestions = document.getElementById('guestSuggestions');
    
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestions.style.display = 'none';
            return;
        }
        
        debounceTimer = setTimeout(() => searchGuests(query), 300);
    });
    
    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchGuests(this.value.trim());
        }
    });
    
    // Hide suggestions on click outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.guest-input-group')) {
            suggestions.style.display = 'none';
        }
    });
}

// Search guests
async function searchGuests(query) {
    try {
        const response = await fetch(`api/recommendation/guest_lookup.php?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            renderGuestSuggestions(data.data);
        } else {
            document.getElementById('guestSuggestions').style.display = 'none';
        }
    } catch (error) {
        console.error('Guest search failed:', error);
    }
}

// Render guest suggestions
function renderGuestSuggestions(guests) {
    const container = document.getElementById('guestSuggestions');
    container.innerHTML = guests.map(guest => `
        <div class="guest-suggestion-item" onclick='selectGuest(${JSON.stringify(guest)})'>
            <div class="fw-bold">${guest.name}</div>
            <small class="text-muted">
                ${guest.nationality ? `<span class="nationality-badge">${guest.nationality}</span>` : ''}
                جواز: ${guest.passportNumber || '-'} | غرفة: ${guest.roomNumber || '-'}
            </small>
        </div>
    `).join('');
    container.style.display = 'block';
}

// Select guest
function selectGuest(guest) {
    selectedGuest = guest;
    
    document.getElementById('guestInput').value = '';
    document.getElementById('guestSuggestions').style.display = 'none';
    
    document.getElementById('selectedGuestName').textContent = guest.name;
    document.getElementById('selectedGuestNationality').textContent = guest.nationality || 'غير محدد';
    document.getElementById('selectedGuestPassport').textContent = guest.passportNumber || '-';
    document.getElementById('selectedGuestRoom').textContent = guest.roomNumber || '-';
    document.getElementById('selectedGuestId').value = guest.id;
    document.getElementById('selectedGuestCard').style.display = 'block';
}

// Clear selected guest
function clearSelectedGuest() {
    selectedGuest = null;
    document.getElementById('selectedGuestCard').style.display = 'none';
    document.getElementById('selectedGuestId').value = '';
}

// Continue without guest (from selection modal)
function continueWithoutGuest() {
    clearSelectedGuest();
    bootstrap.Modal.getInstance(document.getElementById('guestSelectModal')).hide();
    performSearch();
}

// Setup form
function setupForm() {
    document.getElementById('searchForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!selectedCategory) {
            showToast('تنبيه', 'يرجى اختيار فئة للبحث', 'warning');
            return;
        }
        
        performSearch();
    });
}

// Perform search
async function performSearch() {
    showLoading(true);
    
    try {
        const body = {
            category: selectedCategory,
            maxResults: 5
        };
        
        if (selectedGuest) {
            body.guestId = selectedGuest.id;
        }
        
        const response = await fetch('api/recommendation/search.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        
        const data = await response.json();
        
        if (data.success) {
            searchResults = data.data;
            showResults(data.data);
        } else {
            if (data.errorCode === 'HOTEL_NOT_CONFIGURED') {
                showToast('خطأ', 'موقع الفندق غير مُعد. يرجى طلب الإدارة لتحديده.', 'warning');
            } else {
                showToast('خطأ', data.error || 'فشل في البحث', 'danger');
            }
        }
    } catch (error) {
        console.error('Search failed:', error);
        showToast('خطأ', 'حدث خطأ في الاتصال', 'danger');
    } finally {
        showLoading(false);
    }
}

// Show results modal
function showResults(data) {
    // Update modal header
    document.getElementById('resultsCategoryName').textContent = data.categoryLabel;
    document.getElementById('placesCount').textContent = data.count;
    
    // Update search info
    let infoText = `البحث من موقع الفندق`;
    if (data.nationalityUsed) {
        infoText += ` | تم تفضيل النتائج حسب الجنسية: <strong>${data.nationalityUsed}</strong>`;
    } else {
        infoText += ` | مرتبة حسب المسافة (أقرب أولاً)`;
    }
    document.getElementById('searchInfoText').innerHTML = infoText;
    
    // Render places list
    renderPlacesList(data.places);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('resultsModal'));
    modal.show();
    
    // Initialize map after modal is shown
    document.getElementById('resultsModal').addEventListener('shown.bs.modal', () => {
        initResultsMap(data);
    }, { once: true });
}

// Render places list
function renderPlacesList(places) {
    const container = document.getElementById('placesList');
    
    if (places.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>لم يتم العثور على أماكن</h5>
                <p class="text-muted">جرب فئة أخرى أو تأكد من تحديد موقع الفندق</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = places.map((place, index) => `
        <div class="card place-card mb-2 p-3" data-index="${index}" onclick="selectPlace(${index})">
            <div class="d-flex">
                <div class="place-number me-3">${index + 1}</div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                ${place.name}
                                ${place.nationalityBoosted ? '<span class="boosted-badge ms-1"><i class="fas fa-star"></i> مُفضّل</span>' : ''}
                            </h6>
                            <small class="text-muted">${place.address}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">${place.distanceText}</span>
                            ${place.openNow !== null ? 
                                (place.openNow ? 
                                    '<span class="badge bg-success open-badge mt-1 d-block">مفتوح</span>' : 
                                    '<span class="badge bg-danger open-badge mt-1 d-block">مغلق</span>') 
                                : ''}
                        </div>
                    </div>
                    ${place.rating ? `
                        <div class="mt-1">
                            <span class="place-rating">
                                ${getStarRating(place.rating)}
                            </span>
                            <small class="text-muted">(${place.userRatingsTotal} تقييم)</small>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Get star rating HTML
function getStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalf = rating % 1 >= 0.5;
    let html = '';
    
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="fas fa-star"></i>';
    }
    if (hasHalf) {
        html += '<i class="fas fa-star-half-alt"></i>';
    }
    html += ` ${rating.toFixed(1)}`;
    
    return html;
}

// Initialize results map
function initResultsMap(data) {
    if (!mapsApiKey) {
        document.getElementById('resultsMap').innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                <div class="text-center">
                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                    <p>Google Maps غير متاح</p>
                </div>
            </div>
        `;
        return;
    }
    
    // Load Google Maps if not loaded
    if (typeof google === 'undefined') {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${mapsApiKey}&callback=onResultsMapReady`;
        script.async = true;
        window.onResultsMapReady = () => createResultsMap(data);
        document.head.appendChild(script);
    } else {
        createResultsMap(data);
    }
}

// Create results map
function createResultsMap(data) {
    const mapDiv = document.getElementById('resultsMap');
    
    resultsMap = new google.maps.Map(mapDiv, {
        center: { lat: data.hotel.lat, lng: data.hotel.lng },
        zoom: 14,
        mapTypeControl: false
    });
    
    // Clear old markers
    resultsMarkers.forEach(m => m.setMap(null));
    resultsMarkers = [];
    
    // Hotel marker
    hotelMarker = new google.maps.Marker({
        position: { lat: data.hotel.lat, lng: data.hotel.lng },
        map: resultsMap,
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        },
        title: 'الفندق'
    });
    
    // Place markers
    const bounds = new google.maps.LatLngBounds();
    bounds.extend(hotelMarker.getPosition());
    
    data.places.forEach((place, index) => {
        const marker = new google.maps.Marker({
            position: { lat: place.latitude, lng: place.longitude },
            map: resultsMap,
            label: {
                text: String(index + 1),
                color: 'white'
            },
            title: place.name
        });
        
        marker.addListener('click', () => selectPlace(index));
        resultsMarkers.push(marker);
        bounds.extend(marker.getPosition());
    });
    
    resultsMap.fitBounds(bounds);
}

// Select a place
function selectPlace(index) {
    selectedPlace = searchResults.places[index];
    
    // Update UI
    document.querySelectorAll('.place-card').forEach((card, i) => {
        card.classList.toggle('selected', i === index);
    });
    
    // Show directions card
    document.getElementById('selectedPlaceName').textContent = selectedPlace.name;
    document.getElementById('selectedPlaceDistance').textContent = selectedPlace.distanceText;
    
    // Generate Google Maps link
    const hotelLat = searchResults.hotel.lat;
    const hotelLng = searchResults.hotel.lng;
    const destLat = selectedPlace.latitude;
    const destLng = selectedPlace.longitude;
    
    const directionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${hotelLat},${hotelLng}&destination=${destLat},${destLng}&travelmode=driving`;
    
    document.getElementById('openMapsBtn').href = directionsUrl;
    document.getElementById('directionsCard').style.display = 'block';
    
    // Highlight marker on map
    if (resultsMap && resultsMarkers[index]) {
        resultsMap.setCenter(resultsMarkers[index].getPosition());
        resultsMap.setZoom(16);
    }
}

// Copy link button
document.getElementById('copyLinkBtn')?.addEventListener('click', function() {
    const link = document.getElementById('openMapsBtn').href;
    navigator.clipboard.writeText(link).then(() => {
        showToast('نجاح', 'تم نسخ الرابط', 'success');
    }).catch(() => {
        showToast('خطأ', 'فشل في نسخ الرابط', 'danger');
    });
});

// Show/hide loading
function showLoading(show) {
    document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
}

// Show toast
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    
    toast.className = 'toast';
    if (type === 'success') toast.classList.add('border-success');
    else if (type === 'danger') toast.classList.add('border-danger');
    else if (type === 'warning') toast.classList.add('border-warning');
    
    new bootstrap.Toast(toast).show();
}
</script>

</body>
</html>
