<?php
/**
 * Directions Link Generator — OpenStreetMap OSRM routing
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
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

foreach (['hotelLat', 'hotelLng', 'destinationLat', 'destinationLng'] as $f) {
    if (!isset($input[$f])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing: $f"]);
        exit;
    }
}

$oLat = (float)$input['hotelLat'];
$oLng = (float)$input['hotelLng'];
$dLat = (float)$input['destinationLat'];
$dLng = (float)$input['destinationLng'];
$name = $input['placeName'] ?? '';

// OSM routing directions
$directionsUrl = "https://www.openstreetmap.org/directions"
    . "?engine=fossgis_osrm_car"
    . "&route=$oLat,$oLng;$dLat,$dLng";

// Destination pin on OSM
$placeUrl = "https://www.openstreetmap.org/?mlat=$dLat&mlon=$dLng&zoom=17";

echo json_encode([
    'success' => true,
    'data'    => [
        'directionsUrl' => $directionsUrl,
        'placeUrl'      => $placeUrl,
        'placeName'     => $name,
        'origin'        => ['lat' => $oLat, 'lng' => $oLng],
        'destination'   => ['lat' => $dLat, 'lng' => $dLng]
    ]
]);
?>
