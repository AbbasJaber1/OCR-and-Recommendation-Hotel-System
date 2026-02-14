<?php
$directory = __DIR__ . "/../label"; // ✅ Moves up one level to V5/label
$folders = array();

if (!is_dir($directory)) {
    die(json_encode(["error" => "❌ The directory does not exist: " . $directory], JSON_UNESCAPED_UNICODE));
}

// Scan directory
$allFiles = scandir($directory);
$folders = array_values(array_filter($allFiles, function ($folder) use ($directory) {
    return is_dir("$directory/$folder") && $folder !== "." && $folder !== "..";
}));

// Return JSON response
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($folders, JSON_UNESCAPED_UNICODE);
?>

