<?php
// api/contact.php — Save contact form submission to database

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$name    = isset($data['name'])    ? trim($data['name'])    : '';
$email   = isset($data['email'])   ? trim($data['email'])   : '';
$message = isset($data['message']) ? trim($data['message']) : '';

// --- Validation ---
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit();
}

if (strlen($name) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name is too long (max 100 characters).']);
    exit();
}

if (strlen($message) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message is too short (minimum 10 characters).']);
    exit();
}

$conn = getDB();

$stmt = $conn->prepare(
    'INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())'
);
$stmt->bind_param('sss', $name, $email, $message);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => "Thanks $name! Your message has been received. We'll get back to you soon."
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}

$stmt->close();
$conn->close();
?>
