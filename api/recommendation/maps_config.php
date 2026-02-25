<?php
/**
 * Maps Config API
 * Returns Maps API key for frontend (secured endpoint)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../env-loader.php';

$apiKey = env('GOOGLE_MAPS_API_KEY', '');

// Check if API key is configured
if (empty($apiKey) || $apiKey === 'your_google_maps_api_key_here') {
    echo json_encode([
        'success' => false,
        'error' => 'Google Maps API key not configured',
        'configured' => false
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => [
        'apiKey' => $apiKey,
        'configured' => true
    ]
]);
?>
