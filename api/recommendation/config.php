<?php
/**
 * Recommendation Service Configuration
 * Categories with OSM (Overpass API) tags for open-source maps
 */

define('RECOMMENDATION_CATEGORIES', [
    'restaurants' => [
        'label'    => 'مطاعم',
        'label_en' => 'Restaurants',
        'osm'      => [['amenity', 'restaurant']],
        'icon'     => 'utensils'
    ],
    'cafes' => [
        'label'    => 'مقاهي',
        'label_en' => 'Cafes',
        'osm'      => [['amenity', 'cafe'], ['amenity', 'bakery']],
        'icon'     => 'coffee'
    ],
    'clothing' => [
        'label'    => 'محلات ملابس',
        'label_en' => 'Clothing Shops',
        'osm'      => [['shop', 'clothes'], ['shop', 'shoes'], ['shop', 'fashion']],
        'icon'     => 'tshirt'
    ],
    'playgrounds' => [
        'label'    => 'ملاعب',
        'label_en' => 'Playgrounds',
        'osm'      => [['leisure', 'playground'], ['amenity', 'playground'], ['leisure', 'amusement_arcade']],
        'icon'     => 'child'
    ],
    'pharmacies' => [
        'label'    => 'صيدليات',
        'label_en' => 'Pharmacies',
        'osm'      => [['amenity', 'pharmacy']],
        'icon'     => 'pills'
    ],
    'supermarkets' => [
        'label'    => 'سوبرماركت',
        'label_en' => 'Supermarkets',
        'osm'      => [['shop', 'supermarket'], ['shop', 'convenience'], ['shop', 'grocery']],
        'icon'     => 'shopping-cart'
    ],
    'malls' => [
        'label'    => 'مراكز تسوق',
        'label_en' => 'Malls',
        'osm'      => [['shop', 'mall'], ['shop', 'department_store'], ['building', 'retail']],
        'icon'     => 'building'
    ],
    'museums' => [
        'label'    => 'متاحف',
        'label_en' => 'Museums',
        'osm'      => [['tourism', 'museum'], ['tourism', 'gallery']],
        'icon'     => 'landmark'
    ],
    'parks' => [
        'label'    => 'حدائق',
        'label_en' => 'Parks',
        'osm'      => [['leisure', 'park'], ['leisure', 'garden'], ['tourism', 'attraction']],
        'icon'     => 'tree'
    ],
    'hospitals' => [
        'label'    => 'مستشفيات',
        'label_en' => 'Hospitals',
        'osm'      => [['amenity', 'hospital'], ['amenity', 'clinic'], ['amenity', 'doctors']],
        'icon'     => 'hospital'
    ],
    'banks' => [
        'label'    => 'بنوك',
        'label_en' => 'Banks',
        'osm'      => [['amenity', 'bank'], ['amenity', 'atm']],
        'icon'     => 'university'
    ],
    'gas_stations' => [
        'label'    => 'محطات وقود',
        'label_en' => 'Gas Stations',
        'osm'      => [['amenity', 'fuel']],
        'icon'     => 'gas-pump'
    ]
]);

define('DEFAULT_SEARCH_RADIUS', 5000);
define('DEFAULT_MAX_RESULTS', 5);

function getCategoryInfo($categoryKey) {
    $categories = RECOMMENDATION_CATEGORIES;
    return $categories[$categoryKey] ?? null;
}

function getAllCategories() {
    $categories = [];
    foreach (RECOMMENDATION_CATEGORIES as $key => $info) {
        $categories[] = [
            'value'    => $key,
            'label'    => $info['label'],
            'label_en' => $info['label_en'],
            'icon'     => $info['icon']
        ];
    }
    return $categories;
}
?>
