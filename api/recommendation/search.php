<?php
/**
 * Places Search API — powered by Overpass API (OpenStreetMap)
 * No API key required.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/config.php';

/* ── helpers ─────────────────────────────────────────────────────────────── */

function getNationalityKeywords($conn, $nationality) {
    if (empty($nationality)) return [];
    $stmt = $conn->prepare("SELECT keywords FROM nationality_keywords WHERE LOWER(nationality) = LOWER(?)");
    $stmt->bind_param('s', $nationality);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        $kw = json_decode($row['keywords'], true);
        return is_array($kw) ? $kw : [];
    }
    return [];
}

function getGuestNationality($conn, $guestId) {
    $stmt = $conn->prepare("SELECT nationality FROM real_guests WHERE real_guest_id = ?");
    $stmt->bind_param('i', $guestId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? $row['nationality'] : null;
}

function getHotelCoordinates($conn) {
    $result = $conn->query("SELECT latitude, longitude FROM hotel_location WHERE id = 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lat = (float)$row['latitude'];
        $lng = (float)$row['longitude'];
        return ($lat == 0 && $lng == 0) ? null : ['lat' => $lat, 'lng' => $lng];
    }
    return null;
}

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $R = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

function calculateNationalityBoost($place, $keywords, $category) {
    if (empty($keywords)) return 0;
    $text = strtolower($place['name'] . ' ' . $place['vicinity']);
    $boost = 0;
    foreach ($keywords as $kw) {
        if (strpos($text, strtolower($kw)) !== false) {
            $boost += ($category === 'restaurants') ? 0.3 : 0.1;
        }
    }
    return min($boost, 0.5);
}

/**
 * Query Overpass API for nearby places
 */
function searchPlaces($lat, $lng, $category, $radius = 5000) {
    $info = getCategoryInfo($category);
    if (!$info) return ['error' => 'Invalid category'];

    // Build Overpass QL query
    $parts = [];
    foreach ($info['osm'] as [$key, $value]) {
        $parts[] = "node[\"$key\"=\"$value\"](around:$radius,$lat,$lng);";
        $parts[] = "way[\"$key\"=\"$value\"](around:$radius,$lat,$lng);";
    }
    $query = '[out:json][timeout:30];(' . implode('', $parts) . ');out center tags;';

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://overpass-api.de/api/interpreter',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 35,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => 'data=' . urlencode($query),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_USERAGENT      => 'HotelManagementSystem/1.0'
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err)           return ['error' => 'cURL error: ' . $err];
    if ($httpCode != 200) return ['error' => 'Overpass API returned HTTP ' . $httpCode];

    $data = json_decode($response, true);
    if (!isset($data['elements'])) return ['places' => []];

    $places = [];
    foreach ($data['elements'] as $el) {
        // Node → lat/lon; Way → center.lat/lon
        $elLat = isset($el['lat']) ? (float)$el['lat'] : (float)($el['center']['lat'] ?? 0);
        $elLng = isset($el['lon']) ? (float)$el['lon'] : (float)($el['center']['lon'] ?? 0);
        if ($elLat == 0 && $elLng == 0) continue;

        $tags = $el['tags'] ?? [];
        // Prefer Arabic name, fall back to any name
        $name = $tags['name:ar'] ?? $tags['name'] ?? $tags['name:en'] ?? null;
        if (empty($name)) continue;

        $addr = trim(
            ($tags['addr:street']      ?? '') . ' ' .
            ($tags['addr:housenumber'] ?? '') . ' ' .
            ($tags['addr:city']        ?? '')
        );

        $places[] = [
            'place_id'           => $el['type'] . '/' . $el['id'],
            'name'               => $name,
            'vicinity'           => $addr ?: ($tags['addr:full'] ?? ''),
            'geometry'           => ['location' => ['lat' => $elLat, 'lng' => $elLng]],
            'rating'             => 0,
            'user_ratings_total' => 0,
            'types'              => [$category],
            'opening_hours'      => ['open_now' => null]
        ];
    }

    return ['places' => $places];
}

function rankPlaces($places, $hotelLat, $hotelLng, $keywords, $category, $maxResults) {
    $scored = [];
    foreach ($places as $place) {
        $pLat = $place['geometry']['location']['lat'];
        $pLng = $place['geometry']['location']['lng'];
        $dist = calculateDistance($hotelLat, $hotelLng, $pLat, $pLng);

        $distScore = max(0, 1 - ($dist / 5000));
        $natBoost  = calculateNationalityBoost($place, $keywords, $category);

        $scored[] = [
            'placeId'           => $place['place_id'],
            'name'              => $place['name'],
            'address'           => $place['vicinity'],
            'latitude'          => $pLat,
            'longitude'         => $pLng,
            'distance'          => round($dist),
            'distanceText'      => $dist < 1000
                                    ? round($dist) . ' م'
                                    : round($dist / 1000, 1) . ' كم',
            'rating'            => 0,
            'userRatingsTotal'  => 0,
            'openNow'           => null,
            'types'             => $place['types'],
            'score'             => round($distScore + $natBoost, 3),
            'nationalityBoosted'=> $natBoost > 0
        ];
    }
    usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
    return array_slice($scored, 0, $maxResults);
}

/* ── main ────────────────────────────────────────────────────────────────── */
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $category = $input['category'] ?? '';
    if (empty($category) || !getCategoryInfo($category)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid category is required']);
        exit;
    }

    $hotelCoords = getHotelCoordinates($conn);
    if (!$hotelCoords) {
        http_response_code(400);
        echo json_encode([
            'success'   => false,
            'error'     => 'Hotel location not configured.',
            'errorCode' => 'HOTEL_NOT_CONFIGURED'
        ]);
        exit;
    }

    $maxResults  = min(max(1, (int)($input['maxResults'] ?? DEFAULT_MAX_RESULTS)), 10);
    $nationality = null;

    if (!empty($input['guestId'])) {
        $nationality = getGuestNationality($conn, (int)$input['guestId']);
    }

    $keywords    = $nationality ? getNationalityKeywords($conn, $nationality) : [];
    $radius      = (int)($input['radius'] ?? DEFAULT_SEARCH_RADIUS);

    $result = searchPlaces($hotelCoords['lat'], $hotelCoords['lng'], $category, $radius);

    if (isset($result['error'])) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $result['error']]);
        exit;
    }

    $places = $result['places'];

    if (empty($places)) {
        echo json_encode([
            'success' => true,
            'data'    => [
                'places'        => [],
                'hotel'         => $hotelCoords,
                'category'      => $category,
                'nationalityUsed' => $nationality,
                'count'         => 0
            ]
        ]);
        exit;
    }

    $ranked = rankPlaces($places, $hotelCoords['lat'], $hotelCoords['lng'], $keywords, $category, $maxResults);

    echo json_encode([
        'success' => true,
        'data'    => [
            'places'             => $ranked,
            'hotel'              => $hotelCoords,
            'category'           => $category,
            'categoryLabel'      => getCategoryInfo($category)['label'],
            'nationalityUsed'    => $nationality,
            'nationalityBoosting'=> !empty($keywords),
            'count'              => count($ranked)
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
