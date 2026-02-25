<?php
/**
 * Hotel Location API
 * GET: Retrieve hotel location
 * PUT: Update hotel location (Admin only)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../env-loader.php';
require_once __DIR__ . '/../../connect.php';

/**
 * Get hotel location
 */
function getHotelLocation($conn) {
    $sql = "SELECT id, latitude, longitude, label, updated_by, updated_at FROM hotel_location WHERE id = 1";
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        // Check if location is configured (not default 0,0)
        $isConfigured = ($row['latitude'] != 0 || $row['longitude'] != 0);
        
        return [
            'success' => true,
            'data' => [
                'latitude' => (float)$row['latitude'],
                'longitude' => (float)$row['longitude'],
                'label' => $row['label'],
                'updatedBy' => $row['updated_by'],
                'updatedAt' => $row['updated_at'],
                'isConfigured' => $isConfigured
            ]
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Hotel location not found',
        'data' => null
    ];
}

/**
 * Update hotel location (Admin only)
 */
function updateHotelLocation($conn, $data) {
    // Validate required fields
    if (!isset($data['latitude']) || !isset($data['longitude'])) {
        return [
            'success' => false,
            'error' => 'Latitude and longitude are required'
        ];
    }
    
    $latitude = (float)$data['latitude'];
    $longitude = (float)$data['longitude'];
    $label = isset($data['label']) ? trim($data['label']) : null;
    $updatedBy = isset($data['updatedBy']) ? trim($data['updatedBy']) : 'Admin';
    
    // Validate coordinates
    if ($latitude < -90 || $latitude > 90) {
        return [
            'success' => false,
            'error' => 'Invalid latitude. Must be between -90 and 90'
        ];
    }
    
    if ($longitude < -180 || $longitude > 180) {
        return [
            'success' => false,
            'error' => 'Invalid longitude. Must be between -180 and 180'
        ];
    }
    
    // Update or insert
    $sql = "INSERT INTO hotel_location (id, latitude, longitude, label, updated_by, updated_at) 
            VALUES (1, ?, ?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE 
            latitude = VALUES(latitude), 
            longitude = VALUES(longitude), 
            label = VALUES(label), 
            updated_by = VALUES(updated_by), 
            updated_at = NOW()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ddss', $latitude, $longitude, $label, $updatedBy);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Hotel location updated successfully',
            'data' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'label' => $label,
                'updatedBy' => $updatedBy
            ]
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Failed to update hotel location: ' . $conn->error
    ];
}

// Route request
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $response = getHotelLocation($conn);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                $response = ['success' => false, 'error' => 'Invalid JSON input'];
            } else {
                $response = updateHotelLocation($conn, $input);
            }
            break;
            
        default:
            http_response_code(405);
            $response = ['success' => false, 'error' => 'Method not allowed'];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'error' => 'Server error: ' . $e->getMessage()];
}

echo json_encode($response);
$conn->close();
?>
