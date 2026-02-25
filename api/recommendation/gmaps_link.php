<?php
/**
 * Google Maps Link Generator API
 * Generates directions URL from hotel to destination
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// Validate required fields
$required = ['hotelLat', 'hotelLng', 'destinationLat', 'destinationLng'];
foreach ($required as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

$hotelLat = (float)$input['hotelLat'];
$hotelLng = (float)$input['hotelLng'];
$destLat = (float)$input['destinationLat'];
$destLng = (float)$input['destinationLng'];
$placeName = $input['placeName'] ?? '';

// Validate coordinates
if ($hotelLat < -90 || $hotelLat > 90 || $destLat < -90 || $destLat > 90) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid latitude']);
    exit;
}

if ($hotelLng < -180 || $hotelLng > 180 || $destLng < -180 || $destLng > 180) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid longitude']);
    exit;
}

// Generate Google Maps directions URL
$directionsUrl = "https://www.google.com/maps/dir/?api=1" .
    "&origin=" . urlencode("$hotelLat,$hotelLng") .
    "&destination=" . urlencode("$destLat,$destLng") .
    "&travelmode=driving";

// Alternative: Deep link for mobile apps
$mobileUrl = "https://maps.google.com/maps?saddr=$hotelLat,$hotelLng&daddr=$destLat,$destLng";

// Generate simple location URL (just the destination)
$placeUrl = "https://www.google.com/maps/search/?api=1&query=$destLat,$destLng";

echo json_encode([
    'success' => true,
    'data' => [
        'directionsUrl' => $directionsUrl,
        'mobileUrl' => $mobileUrl,
        'placeUrl' => $placeUrl,
        'placeName' => $placeName,
        'origin' => [
            'lat' => $hotelLat,
            'lng' => $hotelLng
        ],
        'destination' => [
            'lat' => $destLat,
            'lng' => $destLng
        ]
    ]
]);
?>
