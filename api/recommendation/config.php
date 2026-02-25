<?php
/**
 * Recommendation Service Configuration
 * Categories and constants for the recommendation system
 */

// Place categories mapping to Google Places API (New) types
// See: https://developers.google.com/maps/documentation/places/web-service/place-types
define('RECOMMENDATION_CATEGORIES', [
    'restaurants' => [
        'label' => 'مطاعم',
        'label_en' => 'Restaurants',
        'types' => ['restaurant'],
        'icon' => 'utensils'
    ],
    'cafes' => [
        'label' => 'مقاهي',
        'label_en' => 'Cafes',
        'types' => ['cafe', 'bakery'],
        'icon' => 'coffee'
    ],
    'clothing' => [
        'label' => 'محلات ملابس',
        'label_en' => 'Clothing Shops',
        'types' => ['clothing_store', 'shoe_store'],
        'icon' => 'tshirt'
    ],
    'playgrounds' => [
        'label' => 'ملاعب',
        'label_en' => 'Playgrounds',
        'types' => ['park', 'amusement_park'],
        'icon' => 'child'
    ],
    'pharmacies' => [
        'label' => 'صيدليات',
        'label_en' => 'Pharmacies',
        'types' => ['pharmacy'],
        'icon' => 'pills'
    ],
    'supermarkets' => [
        'label' => 'سوبرماركت',
        'label_en' => 'Supermarkets',
        'types' => ['supermarket', 'grocery_store', 'convenience_store'],
        'icon' => 'shopping-cart'
    ],
    'malls' => [
        'label' => 'مراكز تسوق',
        'label_en' => 'Malls',
        'types' => ['shopping_mall', 'department_store'],
        'icon' => 'building'
    ],
    'museums' => [
        'label' => 'متاحف',
        'label_en' => 'Museums',
        'types' => ['museum', 'art_gallery'],
        'icon' => 'landmark'
    ],
    'parks' => [
        'label' => 'حدائق',
        'label_en' => 'Parks',
        'types' => ['park', 'tourist_attraction'],
        'icon' => 'tree'
    ],
    'hospitals' => [
        'label' => 'مستشفيات',
        'label_en' => 'Hospitals',
        'types' => ['hospital', 'doctor'],
        'icon' => 'hospital'
    ],
    'banks' => [
        'label' => 'بنوك',
        'label_en' => 'Banks',
        'types' => ['bank', 'atm'],
        'icon' => 'university'
    ],
    'gas_stations' => [
        'label' => 'محطات وقود',
        'label_en' => 'Gas Stations',
        'types' => ['gas_station'],
        'icon' => 'gas-pump'
    ]
]);

// Default search radius in meters
define('DEFAULT_SEARCH_RADIUS', 5000);

// Maximum results to return
define('DEFAULT_MAX_RESULTS', 5);

// Cache duration in seconds (1 hour)
define('PLACES_CACHE_DURATION', 3600);

/**
 * Get category info by key
 */
function getCategoryInfo($categoryKey) {
    $categories = RECOMMENDATION_CATEGORIES;
    return $categories[$categoryKey] ?? null;
}

/**
 * Get all categories for dropdown
 */
function getAllCategories() {
    $categories = [];
    foreach (RECOMMENDATION_CATEGORIES as $key => $info) {
        $categories[] = [
            'value' => $key,
            'label' => $info['label'],
            'label_en' => $info['label_en'],
            'icon' => $info['icon']
        ];
    }
    return $categories;
}
?>
