<?php
// config/headers.php — Set common headers for all API responses

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');           // Allow all origins (dev)
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
