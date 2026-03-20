<?php
// api/register.php — Handle user registration

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

// --- Validate required fields ---
$required = ['firstName', 'lastName', 'handle', 'email', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
        exit();
    }
}

$firstName   = trim($data['firstName']);
$lastName    = trim($data['lastName']);
$handle      = trim($data['handle']);
$email       = trim($data['email']);
$password    = $data['password'];
$danceStyle  = isset($data['danceStyle']) ? trim($data['danceStyle']) : '';

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit();
}

// Normalize handle — ensure it starts with @
if ($handle[0] !== '@') {
    $handle = '@' . $handle;
}

$conn = getDB();

// --- Check for duplicate email ---
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// --- Check for duplicate handle ---
$stmt = $conn->prepare('SELECT id FROM users WHERE handle = ?');
$stmt->bind_param('s', $handle);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'This handle is already taken. Please choose another.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// --- Hash password and insert user ---
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    'INSERT INTO users (first_name, last_name, handle, email, password, dance_style, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->bind_param('ssssss', $firstName, $lastName, $handle, $email, $hashedPassword, $danceStyle);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You can now log in.',
        'user' => [
            'id'         => $stmt->insert_id,
            'firstName'  => $firstName,
            'lastName'   => $lastName,
            'handle'     => $handle,
            'email'      => $email,
            'danceStyle' => $danceStyle,
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}

$stmt->close();
$conn->close();
?>
