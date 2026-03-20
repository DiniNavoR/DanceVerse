<?php
// api/logout.php — Handle user logout (session-based)

require_once '../config/headers.php';

session_start();
session_unset();
session_destroy();

http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
?>
