<?php
// api/get_messages.php — Fetch all contact messages (admin use)

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$conn = getDB();

$result = $conn->query(
    'SELECT id, name, email, message, is_read, created_at
     FROM contact_messages
     ORDER BY created_at DESC'
);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(['success' => true, 'messages' => $messages, 'total' => count($messages)]);

$conn->close();
?>
