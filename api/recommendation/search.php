<?php
/**
 * Places Search API
 * Search nearby places with optional nationality-based boosting
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../env-loader.php';
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/config.php';

/**
 * Get nationality keywords from database
 */
function getNationalityKeywords($conn, $nationality) {
    if (empty($nationality)) {
        return [];
    }
    
    $sql = "SELECT keywords FROM nationality_keywords WHERE LOWER(nationality) = LOWER(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nationality);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $keywords = json_decode($row['keywords'], true);
        $stmt->close();
        return is_array($keywords) ? $keywords : [];
    }
    
    $stmt->close();
    return [];
}

/**
 * Get guest nationality by ID
 */
function getGuestNationality($conn, $guestId) {
    $sql = "SELECT nationality FROM real_guests WHERE real_guest_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $guestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return $row['nationality'];
    }
    
    $stmt->close();
    return null;
}

/**
 * Get hotel location
 */
function getHotelCoordinates($conn) {
    $sql = "SELECT latitude, longitude FROM hotel_location WHERE id = 1";
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        $lat = (float)$row['latitude'];
        $lng = (float)$row['longitude'];
        
        // Check if configured
        if ($lat == 0 && $lng == 0) {
            return null;
        }
        
        return ['lat' => $lat, 'lng' => $lng];
    }
    
    return null;
}

/**
 * Calculate distance between two coordinates (Haversine formula)
 */
function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371000; // meters
    
    $latDiff = deg2rad($lat2 - $lat1);
    $lngDiff = deg2rad($lng2 - $lng1);
    
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lngDiff / 2) * sin($lngDiff / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earthRadius * $c;
}

/**
 * Calculate nationality boost score
 */
function calculateNationalityBoost($place, $keywords, $category) {
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
            // Higher boost for restaurants (cuisine matching)
            if ($category === 'restaurants') {
                $boost += 0.3;
            } else {
                $boost += 0.1;
            }
        }
    }
    
    // Cap the boost at 0.5
    return min($boost, 0.5);
}

/**
 * Call Google Places API (New)
 * Uses the new Places API endpoint: https://places.googleapis.com/v1/places:searchNearby
 */
function searchPlaces($lat, $lng, $category, $radius = 5000) {
    $apiKey = env('GOOGLE_MAPS_API_KEY', '');
    
    if (empty($apiKey) || $apiKey === 'your_google_maps_api_key_here') {
        return ['error' => 'Google Maps API key not configured'];
    }
    
    $categoryInfo = getCategoryInfo($category);
    if (!$categoryInfo) {
        return ['error' => 'Invalid category'];
    }
    
    // Use all types for the category
    $includedTypes = $categoryInfo['types'];
    
    // New Places API endpoint
    $url = "https://places.googleapis.com/v1/places:searchNearby";
    
    // Request body for new API
    $requestBody = [
        'includedTypes' => $includedTypes,
        'maxResultCount' => 20,
        'locationRestriction' => [
            'circle' => [
                'center' => [
                    'latitude' => $lat,
                    'longitude' => $lng
                ],
                'radius' => (float)$radius
            ]
        ]
    ];
    
    // Fields to return (controls billing)
    $fieldMask = 'places.id,places.displayName,places.formattedAddress,places.location,places.rating,places.userRatingCount,places.types,places.regularOpeningHours';
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestBody),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey,
            'X-Goog-FieldMask: ' . $fieldMask
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => 'API request failed: ' . $error];
    }
    
    $data = json_decode($response, true);
    
    if ($httpCode !== 200) {
        $errorMsg = $data['error']['message'] ?? 'Unknown error';
        $errorStatus = $data['error']['status'] ?? 'UNKNOWN';
        return ['error' => "API error: $errorStatus - $errorMsg"];
    }
    
    // Transform new API response to match our expected format
    $places = [];
    if (isset($data['places']) && is_array($data['places'])) {
        foreach ($data['places'] as $place) {
            $places[] = [
                'place_id' => $place['id'] ?? '',
                'name' => $place['displayName']['text'] ?? 'Unknown',
                'vicinity' => $place['formattedAddress'] ?? '',
                'geometry' => [
                    'location' => [
                        'lat' => $place['location']['latitude'] ?? 0,
                        'lng' => $place['location']['longitude'] ?? 0
                    ]
                ],
                'rating' => $place['rating'] ?? 0,
                'user_ratings_total' => $place['userRatingCount'] ?? 0,
                'types' => $place['types'] ?? [],
                'opening_hours' => [
                    'open_now' => isset($place['regularOpeningHours']['openNow']) 
                        ? $place['regularOpeningHours']['openNow'] 
                        : null
                ]
            ];
        }
    }
    
    if (empty($places)) {
        return ['places' => [], 'message' => 'No places found'];
    }
    
    return ['places' => $places];
}

/**
 * Score and rank places
 */
function rankPlaces($places, $hotelLat, $hotelLng, $keywords, $category, $maxResults) {
    $scored = [];
    
    foreach ($places as $place) {
        $placeLat = $place['geometry']['location']['lat'] ?? 0;
        $placeLng = $place['geometry']['location']['lng'] ?? 0;
        
        $distance = calculateDistance($hotelLat, $hotelLng, $placeLat, $placeLng);
        
        // Distance score (closer = higher, normalized 0-1)
        // 5000m = 0, 0m = 1
        $distanceScore = max(0, 1 - ($distance / 5000));
        
        // Nationality boost
        $nationalityBoost = calculateNationalityBoost($place, $keywords, $category);
        
        // Rating boost (0-1 based on 5-star rating)
        $rating = $place['rating'] ?? 0;
        $ratingBoost = $rating / 5 * 0.2;
        
        // Total score
        $totalScore = $distanceScore + $nationalityBoost + $ratingBoost;
        
        $scored[] = [
            'placeId' => $place['place_id'] ?? '',
            'name' => $place['name'] ?? 'Unknown',
            'address' => $place['vicinity'] ?? '',
            'latitude' => $placeLat,
            'longitude' => $placeLng,
            'distance' => round($distance),
            'distanceText' => $distance < 1000 
                ? round($distance) . ' م' 
                : round($distance / 1000, 1) . ' كم',
            'rating' => $rating,
            'userRatingsTotal' => $place['user_ratings_total'] ?? 0,
            'openNow' => $place['opening_hours']['open_now'] ?? null,
            'types' => $place['types'] ?? [],
            'icon' => $place['icon'] ?? '',
            'photos' => isset($place['photos'][0]['photo_reference']) 
                ? $place['photos'][0]['photo_reference'] 
                : null,
            'score' => round($totalScore, 3),
            'nationalityBoosted' => $nationalityBoost > 0
        ];
    }
    
    // Sort by score descending
    usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
    
    // Return top results
    return array_slice($scored, 0, $maxResults);
}

/**
 * Log recommendation for analytics
 */
function logRecommendation($conn, $guestId, $category, $nationality, $placeName, $placeLat, $placeLng) {
    $sql = "INSERT INTO recommendation_logs (guest_id, category, nationality_used, place_name, place_lat, place_lng) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssdd', $guestId, $category, $nationality, $placeName, $placeLat, $placeLng);
    $stmt->execute();
    $stmt->close();
}

// Main handler
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }
    
    // Validate category (required)
    $category = $input['category'] ?? '';
    if (empty($category) || !getCategoryInfo($category)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Valid category is required',
            'validCategories' => array_keys(RECOMMENDATION_CATEGORIES)
        ]);
        exit;
    }
    
    // Get hotel location
    $hotelCoords = getHotelCoordinates($conn);
    if (!$hotelCoords) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Hotel location not configured. Please ask admin to set the hotel location.',
            'errorCode' => 'HOTEL_NOT_CONFIGURED'
        ]);
        exit;
    }
    
    // Get max results (default 5)
    $maxResults = min(max(1, intval($input['maxResults'] ?? DEFAULT_MAX_RESULTS)), 10);
    
    // Determine nationality (optional)
    $nationality = null;
    $guestId = null;
    $guestName = null;
    
    // If guestId provided, look up nationality
    if (!empty($input['guestId'])) {
        $guestId = (int)$input['guestId'];
        $nationality = getGuestNationality($conn, $guestId);
    }
    
    // Get nationality keywords for boosting
    $keywords = [];
    if ($nationality) {
        $keywords = getNationalityKeywords($conn, $nationality);
    }
    
    // Search places
    $searchResult = searchPlaces(
        $hotelCoords['lat'], 
        $hotelCoords['lng'], 
        $category,
        $input['radius'] ?? DEFAULT_SEARCH_RADIUS
    );
    
    if (isset($searchResult['error'])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $searchResult['error']
        ]);
        exit;
    }
    
    $places = $searchResult['places'];
    
    if (empty($places)) {
        echo json_encode([
            'success' => true,
            'data' => [
                'places' => [],
                'hotel' => $hotelCoords,
                'category' => $category,
                'nationalityUsed' => $nationality,
                'count' => 0
            ],
            'message' => 'No places found in this category nearby'
        ]);
        exit;
    }
    
    // Rank places with scoring
    $rankedPlaces = rankPlaces(
        $places,
        $hotelCoords['lat'],
        $hotelCoords['lng'],
        $keywords,
        $category,
        $maxResults
    );
    
    echo json_encode([
        'success' => true,
        'data' => [
            'places' => $rankedPlaces,
            'hotel' => $hotelCoords,
            'category' => $category,
            'categoryLabel' => getCategoryInfo($category)['label'],
            'nationalityUsed' => $nationality,
            'nationalityBoosting' => !empty($keywords),
            'count' => count($rankedPlaces)
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
