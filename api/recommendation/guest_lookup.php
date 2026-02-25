<?php
/**
 * Guest Lookup API
 * Search guests by name or passport number
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../env-loader.php';
require_once __DIR__ . '/../../connect.php';

/**
 * Search for guests by name or passport number
 */
function searchGuests($conn, $query) {
    if (empty($query) || strlen($query) < 2) {
        return [
            'success' => true,
            'data' => [],
            'message' => 'Query too short'
        ];
    }
    
    $searchTerm = '%' . trim($query) . '%';
    
    // Search in real_guests table
    $sql = "SELECT 
                real_guest_id as id,
                full_name as name,
                nationality,
                passport_number,
                room_number,
                check_in,
                check_out
            FROM real_guests 
            WHERE full_name LIKE ? 
               OR passport_number LIKE ?
            ORDER BY check_in DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $guests = [];
    while ($row = $result->fetch_assoc()) {
        $guests[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'nationality' => $row['nationality'],
            'passportNumber' => $row['passport_number'],
            'roomNumber' => $row['room_number'],
            'checkIn' => $row['check_in'],
            'checkOut' => $row['check_out']
        ];
    }
    
    $stmt->close();
    
    return [
        'success' => true,
        'data' => $guests,
        'count' => count($guests)
    ];
}

/**
 * Get guest by ID
 */
function getGuestById($conn, $id) {
    $sql = "SELECT 
                real_guest_id as id,
                full_name as name,
                nationality,
                passport_number,
                room_number,
                check_in,
                check_out
            FROM real_guests 
            WHERE real_guest_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return [
            'success' => true,
            'data' => [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'nationality' => $row['nationality'],
                'passportNumber' => $row['passport_number'],
                'roomNumber' => $row['room_number'],
                'checkIn' => $row['check_in'],
                'checkOut' => $row['check_out']
            ]
        ];
    }
    
    $stmt->close();
    return [
        'success' => false,
        'error' => 'Guest not found'
    ];
}

// Route request
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
    if (isset($_GET['id'])) {
        // Get specific guest by ID
        $response = getGuestById($conn, (int)$_GET['id']);
    } elseif (isset($_GET['query'])) {
        // Search guests
        $response = searchGuests($conn, $_GET['query']);
    } else {
        $response = [
            'success' => false,
            'error' => 'Missing query parameter. Use ?query=... or ?id=...'
        ];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'error' => 'Server error: ' . $e->getMessage()];
}

echo json_encode($response);
$conn->close();
?>
