<?php
// Load environment variables
require_once __DIR__ . '/env-loader.php';

// Database configuration from .env file
$host = env('DB_HOST', 'localhost');
$username = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');
$database = env('DB_NAME', 'hotel-check-in/out');

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
