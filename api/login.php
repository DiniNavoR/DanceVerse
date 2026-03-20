<?php
// api/login.php — Handle user login

require_once '../config/headers.php';
require_once '../config/db.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

// Read JSON body
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit();
}

$email    = trim($data['email']);
$password = $data['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit();
}

$conn = getDB();

// Fetch user by email
$stmt = $conn->prepare(
    'SELECT id, first_name, last_name, handle, email, password, dance_style FROM users WHERE email = ?'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Use a generic message to avoid user enumeration
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    $stmt->close();
    $conn->close();
    exit();
}

$user = $result->fetch_assoc();

// Verify password against bcrypt hash
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    $stmt->close();
    $conn->close();
    exit();
}

// --- Login successful ---
// Return user data (never return the password hash)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Login successful! Welcome back, ' . $user['first_name'] . '!',
    'user' => [
        'id'         => $user['id'],
        'firstName'  => $user['first_name'],
        'lastName'   => $user['last_name'],
        'handle'     => $user['handle'],
        'email'      => $user['email'],
        'danceStyle' => $user['dance_style'],
    ]
]);

$stmt->close();
$conn->close();
?>
