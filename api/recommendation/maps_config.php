<?php
/**
 * Maps Config API — OpenStreetMap / Leaflet (no API key required)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

echo json_encode([
    'success' => true,
    'data'    => [
        'configured' => true,
        'provider'   => 'openstreetmap'
    ]
]);
?>
