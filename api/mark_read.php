<?php
// api/mark_read.php — Mark a contact message as read

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id']) || !is_numeric($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid message ID is required.']);
    exit();
}

$id = (int) $data['id'];
$conn = getDB();

$stmt = $conn->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Message marked as read.']);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Message not found.']);
}

$stmt->close();
$conn->close();
?>
