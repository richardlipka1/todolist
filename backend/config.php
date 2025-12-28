<?php
// Database configuration - supports both local and Docker environments
define('DB_HOST', 'db.r4.websupport.sk');
define('DB_USER', 'safat456');
define('DB_PASS', 'jrqj!#Wr5n2|la`ER^/E');
define('DB_NAME', 'todolist');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}

// Enable CORS for React frontend
// SECURITY NOTE: In production, replace '*' with specific allowed origins
// Example: header('Access-Control-Allow-Origin: https://yourdomain.com');
$allowed_origin = getenv('ALLOWED_ORIGIN') ?: '*';
header('Access-Control-Allow-Origin: ' . $allowed_origin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
