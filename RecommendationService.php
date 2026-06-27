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
  <title>خدمة التوصيات — الفندق</title>
  <?php include 'includes/head.php'; ?>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <style>
    /* Loading overlay */
    #loadingOverlay {
      position: fixed;
      inset: 0;
      background: rgba(5,46,10,.65);
      backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .loading-spinner {
      background: var(--s-0);
      padding: 40px 48px;
      border-radius: var(--r-2xl);
      text-align: center;
      box-shadow: var(--sh-xl);
      border: 1px solid var(--bd-1);
    }

    /* Guest autocomplete */
    .guest-input-group { position: relative; }
    .guest-suggestions {
      position: absolute;
      top: calc(100% + 6px);
      left: 0; right: 0;
      z-index: 1000;
      max-height: 300px;
      overflow-y: auto;
      background: var(--s-0);
      border: 1.5px solid var(--bd-2);
      border-radius: var(--r-lg);
      box-shadow: var(--sh-lg);
      display: none;
    }
    .guest-suggestion-item {
      padding: 12px 16px;
      cursor: pointer;
      border-bottom: 1px solid var(--bd-1);
      transition: var(--tf);
      font-size: .875rem;
    }
    .guest-suggestion-item:hover { background: var(--g-25); }
    .guest-suggestion-item:last-child { border-bottom: none; }

    /* Selected guest card */
    .selected-guest {
      background: linear-gradient(135deg, var(--g-50), var(--g-25));
      border: 1.5px solid var(--g-200);
      border-radius: var(--r-lg);
      padding: 14px 16px;
      margin-top: 10px;
    }

    /* Category cards */
    .category-card {
      border-radius: var(--r-xl);
      border: 2px solid var(--bd-1);
      padding: 20px 10px;
      text-align: center;
      cursor: pointer;
      transition: all .25s cubic-bezier(.4,0,.2,1);
      background: var(--s-0);
    }
    .category-card:hover {
      border-color: var(--g-400);
      transform: translateY(-3px);
      box-shadow: var(--sh-md);
    }
    .category-card.selected {
      border-color: var(--g-600);
      background: var(--g-50);
      box-shadow: 0 0 0 3px rgba(46,125,50,.14);
    }
    .category-icon {
      width: 54px; height: 54px;
      display: flex; align-items: center; justify-content: center;
      border-radius: var(--r-lg);
      font-size: 1.4rem;
      margin: 0 auto 10px;
      color: #fff;
    }

    /* Results modal */
    .results-modal .modal-content { border-radius: var(--r-2xl); border: none; overflow: hidden; }
    .results-modal .modal-header {
      background: linear-gradient(135deg, var(--g-800), var(--g-600));
      color: #fff;
      border-radius: 0;
    }
    .results-map { height: 350px; border-radius: var(--r-lg); overflow: hidden; z-index: 0; }
    .leaflet-attribution-flag { display: none !important; }

    /* Place cards */
    .place-card {
      cursor: pointer;
      transition: var(--t);
      border: 2px solid var(--bd-1);
      border-radius: var(--r-lg);
    }
    .place-card:hover { border-color: var(--g-400); background: var(--g-25); }
    .place-card.selected { border-color: var(--g-500); background: var(--g-50); }
    .place-number {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--g-500), var(--g-700));
      color: #fff; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .place-rating { color: var(--au-400); }
    .open-badge { font-size: .73rem; }

    /* Directions card */
    .directions-card {
      background: linear-gradient(135deg, var(--g-700), var(--g-900));
      color: #fff;
      border-radius: var(--r-xl);
    }

    /* Nationality & boosted badges */
    .nationality-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: var(--r-full);
      font-size: .8rem;
      background: var(--au-50);
      color: var(--au-700);
      border: 1px solid var(--au-100);
    }
    .boosted-badge {
      background: var(--g-50);
      color: var(--g-700);
      border: 1px solid var(--g-100);
      font-size: .7rem;
      padding: 2px 8px;
      border-radius: var(--r-full);
    }

    /* Optional label */
    .optional-label { font-size: .78rem; color: var(--tx-3); font-weight: 400; }
  </style>
</head>
<body>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display:none">
  <div class="loading-spinner">
    <div class="hs-spin hs-spin-lg" style="margin:0 auto 20px;border-top-color:var(--g-500)"></div>
    <h5 style="color:var(--tx-1);margin-bottom:6px" data-i18n="searching">جاري البحث...</h5>
    <p style="color:var(--tx-3);font-size:.875rem;margin:0" data-i18n="please_wait">يرجى الانتظار</p>
  </div>
</div>

<div class="hs-app">
  <?php include 'includes/sidebar.php'; ?>

  <div class="hs-main" id="mainContent">
    <header class="hs-topbar">
      <div class="hs-topbar-start">
        <button class="hs-mob-btn" id="mobMenuBtn"><i class="fas fa-bars"></i></button>
        <div>
          <div class="hs-pg-title" data-i18n="rec_title">خدمة التوصيات</div>
          <div class="hs-pg-sub" data-i18n="rec_sub">ابحث عن أفضل الأماكن القريبة للضيوف</div>
        </div>
      </div>
      <div class="hs-topbar-end">
        <button class="hs-icon-btn"><i class="fas fa-bell"></i><span class="hs-notif-dot"></span></button>
        <div class="hs-user-pill">
          <div class="hs-avatar"><i class="fas fa-user" style="font-size:10px"></i></div>
          <span class="hs-uname" data-i18n="employee">موظف</span>
        </div>
      </div>
    </header>

    <main class="hs-content hs-stagger">

      <!-- Hero Banner -->
      <div class="hs-card hs-mb-6" style="background:linear-gradient(135deg,var(--g-800),var(--g-600));color:#fff;border:none">
        <div class="hs-card-bd">
          <div style="display:flex;align-items:center;gap:20px">
            <div style="width:60px;height:60px;border-radius:var(--r-xl);background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0">
              <i class="fas fa-compass"></i>
            </div>
            <div>
              <h2 style="color:#fff;margin-bottom:4px;font-size:1.25rem" data-i18n="rec_hero_title">خدمة توصيات الأماكن القريبة</h2>
              <p style="color:rgba(255,255,255,.65);margin:0;font-size:.875rem" data-i18n="rec_hero_sub">ابحث عن أفضل الأماكن للضيوف</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Form Card -->
      <div class="hs-card">
        <div class="hs-card-hd">
          <div class="hs-card-title">
            <div class="hs-card-ic"><i class="fas fa-search"></i></div>
            <span data-i18n="rec_search_title">بحث عن أماكن قريبة</span>
          </div>
        </div>
        <div class="hs-card-bd">
          <form id="searchForm">

            <!-- Guest Input -->
            <div class="hs-form-g">
              <label class="hs-lbl">
                <i class="fas fa-user" style="color:var(--g-600);margin-left:6px"></i>
                <span data-i18n="rec_guest">اسم الضيف أو رقم الجواز (اختياري)</span>
                <span class="optional-label" data-i18n="guest_opt_note">(اختياري — يُستخدم لتخصيص النتائج حسب الجنسية)</span>
              </label>
              <div class="guest-input-group">
                <div class="hs-search-bar">
                  <i class="hs-search-ic fas fa-user"></i>
                  <input type="text" id="guestInput" class="hs-search-in"
                         placeholder="ابحث عن ضيف بالاسم أو رقم الجواز..."
                         autocomplete="off">
                </div>
                <div class="guest-suggestions" id="guestSuggestions"></div>
              </div>

              <!-- Selected Guest Card -->
              <div class="selected-guest" id="selectedGuestCard" style="display:none">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                  <div style="display:flex;align-items:center;gap:10px">
                    <strong id="selectedGuestName" style="color:var(--tx-1)"></strong>
                    <span class="nationality-badge" id="selectedGuestNationality"></span>
                  </div>
                  <button type="button" class="hs-btn hs-btn-danger hs-btn-sm hs-btn-ic" onclick="clearSelectedGuest()" style="width:28px;height:28px;padding:0">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <small style="color:var(--tx-3);display:flex;align-items:center;gap:8px">
                  <i class="fas fa-id-card"></i>
                  <span id="selectedGuestPassport"></span>
                  <span style="color:var(--bd-2)">|</span>
                  <i class="fas fa-door-open"></i>
                  <span data-i18n="room_lbl">غرفة:</span> <span id="selectedGuestRoom"></span>
                </small>
                <input type="hidden" id="selectedGuestId" name="guestId">
              </div>
            </div>

            <!-- Category Selection -->
            <div class="hs-form-g">
              <label class="hs-lbl">
                <i class="fas fa-th-large" style="color:var(--g-600);margin-left:6px"></i>
                <span data-i18n="rec_cat">اختر الفئة</span> <span style="color:#DC2626">*</span>
              </label>
              <div class="row g-3" id="categoriesGrid">
                <!-- Categories loaded by JS -->
              </div>
              <input type="hidden" id="selectedCategory" name="category" required>
            </div>

            <!-- Search Button -->
            <div style="text-align:center;margin-top:8px">
              <button type="submit" class="hs-btn hs-btn-primary hs-btn-lg" id="searchBtn" disabled>
                <i class="fas fa-search"></i> <span data-i18n="rec_btn">بحث عن الأماكن القريبة</span>
              </button>
              <p style="color:var(--tx-3);font-size:.8rem;margin-top:10px;margin-bottom:0">
                <i class="fas fa-info-circle"></i> <span data-i18n="rec_search_info">يجب اختيار فئة للبحث. بيانات الضيف اختيارية.</span>
              </p>
            </div>

          </form>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- Results Modal -->
<div class="modal fade results-modal" id="resultsModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="display:flex;align-items:center;gap:10px">
          <i class="fas fa-map-marked-alt"></i>
          <span data-i18n="results_search">نتائج البحث</span> — <span id="resultsCategoryName"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">

        <div class="alert alert-info mb-4" id="searchInfoAlert">
          <div style="display:flex;align-items:center;gap:14px">
            <i class="fas fa-info-circle fa-2x"></i>
            <div>
              <strong data-i18n="search_info_title">معلومات البحث</strong>
              <p class="mb-0 small" id="searchInfoText"></p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="results-map" id="resultsMap"></div>
          </div>
          <div class="col-lg-6">
            <h6 class="mb-3">
              <i class="fas fa-list-ol me-2"></i>
              <span data-i18n="nearby_places_lbl">الأماكن القريبة</span> (<span id="placesCount">0</span>)
            </h6>
            <div id="placesList"></div>
          </div>
        </div>

        <!-- Directions Card -->
        <div class="directions-card p-4 mt-4" id="directionsCard" style="display:none">
          <div class="row align-items-center">
            <div class="col-md-6">
              <h5 style="color:#fff"><i class="fas fa-directions me-2"></i><span data-i18n="directions_to">الاتجاهات إلى</span></h5>
              <h4 id="selectedPlaceName" style="color:#fff;margin-bottom:4px"></h4>
              <small id="selectedPlaceDistance" style="color:rgba(255,255,255,.7)"></small>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
              <a href="#" id="openMapsBtn" target="_blank" class="btn btn-light btn-lg me-2">
                <i class="fas fa-map me-2"></i> <span data-i18n="open_osm">فتح في OpenStreetMap</span>
              </a>
              <button type="button" class="btn btn-outline-light" id="copyLinkBtn">
                <i class="fas fa-copy me-2"></i> <span data-i18n="copy_link_lbl">نسخ الرابط</span>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Guest Selection Modal -->
<div class="modal fade" id="guestSelectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:var(--r-xl);overflow:hidden">
      <div class="modal-header" style="background:var(--g-800);color:#fff">
        <h5 class="modal-title"><i class="fas fa-users me-2"></i><span data-i18n="select_guest_title">اختر الضيف</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted" data-i18n="multiple_guests_msg">تم العثور على عدة ضيوف. يرجى اختيار الضيف المطلوب:</p>
        <div id="guestSelectList"></div>
        <div class="text-center mt-3">
          <button type="button" class="btn btn-secondary" onclick="continueWithoutGuest()" data-i18n="continue_no_guest">
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
      <i class="fas fa-bell me-2 text-success"></i>
      <strong class="me-auto" id="toastTitle" data-i18n="notification">إشعار</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toastMessage"></div>
  </div>
</div>

<div class="hs-toast-ctr"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// State
let selectedGuest = null;
let selectedCategory = null;
let searchResults = null;
let resultsMap = null;
let resultsMarkers = [];
let hotelMarker = null;
let selectedPlace = null;
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
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    setupGuestSearch();
    setupForm();
});

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
        showToast(Lang.t('err_title'), Lang.t('cat_load_fail'), 'danger');
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
                ${Lang.t('passport_lbl')} ${guest.passportNumber || '-'} | ${Lang.t('room_lbl')} ${guest.roomNumber || '-'}
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
    document.getElementById('selectedGuestNationality').textContent = guest.nationality || Lang.t('unknown_nat');
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
            showToast(Lang.t('warning_title'), Lang.t('select_cat_warn'), 'warning');
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
                showToast(Lang.t('err_title'), Lang.t('hotel_not_conf_msg'), 'warning');
            } else {
                showToast(Lang.t('err_title'), data.error || Lang.t('search_error'), 'danger');
            }
        }
    } catch (error) {
        console.error('Search failed:', error);
        showToast(Lang.t('err_title'), Lang.t('conn_error'), 'danger');
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
    let infoText = Lang.t('info_from_hotel');
    if (data.nationalityUsed) {
        infoText += ` | ${Lang.t('info_nat_boost')} <strong>${data.nationalityUsed}</strong>`;
    } else {
        infoText += ` | ${Lang.t('info_by_dist')}`;
    }
    document.getElementById('searchInfoText').innerHTML = infoText;

    // Render places list
    renderPlacesList(data.places);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('resultsModal'));
    modal.show();

    // Init map after modal finishes opening (so the container has real dimensions)
    document.getElementById('resultsModal').addEventListener('shown.bs.modal', () => {
        initResultsMap(data);
        if (resultsMap) resultsMap.invalidateSize();
    }, { once: true });
}

// Render places list
function renderPlacesList(places) {
    const container = document.getElementById('placesList');

    if (places.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>${Lang.t('no_places')}</h5>
                <p class="text-muted">${Lang.t('try_category')}</p>
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
                                ${place.nationalityBoosted ? `<span class="boosted-badge ms-1"><i class="fas fa-star"></i> ${Lang.t('boosted_lbl')}</span>` : ''}
                            </h6>
                            <small class="text-muted">${place.address}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">${place.distanceText}</span>
                            ${place.openNow !== null ?
                                (place.openNow ?
                                    `<span class="badge bg-success open-badge mt-1 d-block">${Lang.t('st_open')}</span>` :
                                    `<span class="badge bg-danger open-badge mt-1 d-block">${Lang.t('st_closed')}</span>`)
                                : ''}
                        </div>
                    </div>
                    ${place.rating ? `
                        <div class="mt-1">
                            <span class="place-rating">
                                ${getStarRating(place.rating)}
                            </span>
                            <small class="text-muted">(${place.userRatingsTotal} ${Lang.t('ratings_count')})</small>
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

// Initialize / recreate results map with Leaflet
function initResultsMap(data) {
    // Destroy old map instance to avoid "already initialized" error
    if (resultsMap) {
        resultsMap.remove();
        resultsMap = null;
        resultsMarkers = [];
    }
    createResultsMap(data);
}

function createResultsMap(data) {
    const mapDiv = document.getElementById('resultsMap');

    resultsMap = L.map(mapDiv).setView([data.hotel.lat, data.hotel.lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(resultsMap);

    // Hotel marker
    const hotelIcon = L.divIcon({
        className: '',
        html: `<div style="background:linear-gradient(135deg,#1D4ED8,#1e40af);color:#fff;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.4)"><i class="fas fa-hotel"></i></div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });
    hotelMarker = L.marker([data.hotel.lat, data.hotel.lng], { icon: hotelIcon })
        .addTo(resultsMap)
        .bindPopup(Lang.t('hotel_lbl'));

    // Place markers
    const bounds = L.latLngBounds([[data.hotel.lat, data.hotel.lng]]);

    data.places.forEach((place, index) => {
        const placeIcon = L.divIcon({
            className: '',
            html: `<div style="background:linear-gradient(135deg,var(--g-500),var(--g-700));color:#fff;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.35)">${index + 1}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const m = L.marker([place.latitude, place.longitude], { icon: placeIcon })
            .addTo(resultsMap)
            .bindPopup(`<strong>${place.name}</strong><br>${place.distanceText}`)
            .on('click', () => selectPlace(index));

        resultsMarkers.push(m);
        bounds.extend([place.latitude, place.longitude]);
    });

    resultsMap.fitBounds(bounds, { padding: [30, 30] });
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

    // Generate OSM directions link
    const hotelLat = searchResults.hotel.lat;
    const hotelLng = searchResults.hotel.lng;
    const destLat  = selectedPlace.latitude;
    const destLng  = selectedPlace.longitude;

    const directionsUrl = `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${hotelLat},${hotelLng};${destLat},${destLng}`;

    document.getElementById('openMapsBtn').href = directionsUrl;
    document.getElementById('directionsCard').style.display = 'block';

    // Highlight marker on map
    if (resultsMap && resultsMarkers[index]) {
        resultsMap.setView(resultsMarkers[index].getLatLng(), 16);
        resultsMarkers[index].openPopup();
    }
}

// Copy link button
document.getElementById('copyLinkBtn')?.addEventListener('click', function() {
    const link = document.getElementById('openMapsBtn').href;
    navigator.clipboard.writeText(link).then(() => {
        showToast(Lang.t('success_title'), Lang.t('link_copied'), 'success');
    }).catch(() => {
        showToast(Lang.t('err_title'), Lang.t('link_copy_fail'), 'danger');
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
