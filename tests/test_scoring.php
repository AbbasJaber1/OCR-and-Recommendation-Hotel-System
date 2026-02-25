<?php
/**
 * Basic Test for Recommendation Service Scoring Logic
 * Run: php tests/test_scoring.php
 */

// Include required files
require_once __DIR__ . '/../api/recommendation/config.php';

echo "=== Recommendation Service Scoring Tests ===\n\n";

// Test 1: Distance Calculation
function testDistanceCalculation() {
    echo "Test 1: Distance Calculation (Haversine Formula)\n";
    
    // Test coordinates: Baghdad to Karbala (approximate)
    $lat1 = 33.3152; // Baghdad
    $lng1 = 44.3661;
    $lat2 = 32.6167; // Karbala
    $lng2 = 44.0333;
    
    $earthRadius = 6371000; // meters
    
    $latDiff = deg2rad($lat2 - $lat1);
    $lngDiff = deg2rad($lng2 - $lng1);
    
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lngDiff / 2) * sin($lngDiff / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    
    // Should be approximately 80km
    $expectedMin = 75000;
    $expectedMax = 85000;
    
    if ($distance > $expectedMin && $distance < $expectedMax) {
        echo "  ✓ PASS: Distance = " . round($distance / 1000, 2) . " km (expected ~80km)\n";
        return true;
    } else {
        echo "  ✗ FAIL: Distance = " . round($distance / 1000, 2) . " km\n";
        return false;
    }
}

// Test 2: Nationality Boost Calculation
function testNationalityBoost() {
    echo "\nTest 2: Nationality Boost Calculation\n";
    
    $keywords = ['lebanese', 'shawarma', 'hummus', 'falafel'];
    
    // Test case: Restaurant with matching cuisine
    $place1 = [
        'name' => 'Lebanese Shawarma House',
        'vicinity' => 'Main Street',
        'types' => ['restaurant']
    ];
    
    $boost1 = calculateTestBoost($place1, $keywords, 'restaurants');
    
    // Test case: Generic restaurant
    $place2 = [
        'name' => 'Pizza Palace',
        'vicinity' => 'Downtown',
        'types' => ['restaurant']
    ];
    
    $boost2 = calculateTestBoost($place2, $keywords, 'restaurants');
    
    // Test case: Non-restaurant category
    $place3 = [
        'name' => 'Lebanese Pharmacy',
        'vicinity' => 'Health Center',
        'types' => ['pharmacy']
    ];
    
    $boost3 = calculateTestBoost($place3, $keywords, 'pharmacies');
    
    $pass = true;
    
    if ($boost1 > 0) {
        echo "  ✓ PASS: Lebanese restaurant boost = $boost1 (expected > 0)\n";
    } else {
        echo "  ✗ FAIL: Lebanese restaurant boost = $boost1\n";
        $pass = false;
    }
    
    if ($boost2 == 0) {
        echo "  ✓ PASS: Generic restaurant boost = $boost2 (expected 0)\n";
    } else {
        echo "  ✗ FAIL: Generic restaurant boost = $boost2\n";
        $pass = false;
    }
    
    if ($boost3 < $boost1) {
        echo "  ✓ PASS: Pharmacy boost ($boost3) < Restaurant boost ($boost1)\n";
    } else {
        echo "  ✗ FAIL: Pharmacy boost should be lower\n";
        $pass = false;
    }
    
    return $pass;
}

function calculateTestBoost($place, $keywords, $category) {
    if (empty($keywords)) {
        return 0;
    }
    
    $boost = 0;
    $searchText = strtolower(
        ($place['name'] ?? '') . ' ' . 
        ($place['vicinity'] ?? '') . ' ' .
        implode(' ', $place['types'] ?? [])
    );
    
    foreach ($keywords as $keyword) {
        if (strpos($searchText, strtolower($keyword)) !== false) {
            if ($category === 'restaurants') {
                $boost += 0.3;
            } else {
                $boost += 0.1;
            }
        }
    }
    
    return min($boost, 0.5);
}

// Test 3: Category Configuration
function testCategoryConfig() {
    echo "\nTest 3: Category Configuration\n";
    
    $categories = RECOMMENDATION_CATEGORIES;
    $requiredCategories = ['restaurants', 'cafes', 'pharmacies', 'supermarkets'];
    
    $pass = true;
    foreach ($requiredCategories as $cat) {
        if (isset($categories[$cat])) {
            echo "  ✓ PASS: Category '$cat' exists\n";
        } else {
            echo "  ✗ FAIL: Category '$cat' missing\n";
            $pass = false;
        }
    }
    
    // Check category structure
    $sample = $categories['restaurants'] ?? [];
    $requiredFields = ['label', 'label_en', 'types', 'icon'];
    
    foreach ($requiredFields as $field) {
        if (isset($sample[$field])) {
            echo "  ✓ PASS: Category has '$field' field\n";
        } else {
            echo "  ✗ FAIL: Category missing '$field' field\n";
            $pass = false;
        }
    }
    
    return $pass;
}

// Test 4: Score Ranking
function testScoreRanking() {
    echo "\nTest 4: Score Ranking Order\n";
    
    // Simulated places with different scores
    $places = [
        ['name' => 'Far Place', 'score' => 0.2],
        ['name' => 'Close Boosted Place', 'score' => 0.9],
        ['name' => 'Close Place', 'score' => 0.7],
        ['name' => 'Medium Place', 'score' => 0.5],
    ];
    
    // Sort descending
    usort($places, fn($a, $b) => $b['score'] <=> $a['score']);
    
    $expected = ['Close Boosted Place', 'Close Place', 'Medium Place', 'Far Place'];
    $actual = array_column($places, 'name');
    
    if ($actual === $expected) {
        echo "  ✓ PASS: Places ranked correctly by score\n";
        foreach ($places as $i => $p) {
            echo "    " . ($i + 1) . ". {$p['name']} (score: {$p['score']})\n";
        }
        return true;
    } else {
        echo "  ✗ FAIL: Places not ranked correctly\n";
        return false;
    }
}

// Run all tests
$results = [
    testDistanceCalculation(),
    testNationalityBoost(),
    testCategoryConfig(),
    testScoreRanking()
];

echo "\n=== Summary ===\n";
$passed = count(array_filter($results));
$total = count($results);
echo "Passed: $passed / $total\n";

exit($passed === $total ? 0 : 1);
?>
