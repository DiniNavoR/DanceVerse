<?php
// api/submit_challenge.php — Handle video submission for weekly challenge

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

// --- Validate user is logged in (sent from frontend via POST field) ---
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$handle = isset($_POST['handle'])  ? trim($_POST['handle'])  : '';

if (!$userId || empty($handle)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a video.']);
    exit();
}

// --- Validate challenge ID ---
$challengeId = isset($_POST['challenge_id']) ? (int)$_POST['challenge_id'] : 1;

// --- Validate uploaded file ---
if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form size limit.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
    ];
    $errorCode = $_FILES['video']['error'] ?? UPLOAD_ERR_NO_FILE;
    $errorMsg  = $uploadErrors[$errorCode] ?? 'Unknown upload error.';
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $errorMsg]);
    exit();
}

$file         = $_FILES['video'];
$originalName = basename($file['name']);
$fileSize     = $file['size'];
$tmpPath      = $file['tmp_name'];

// Allowed MIME types
$allowedMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo'];
$finfo        = finfo_open(FILEINFO_MIME_TYPE);
$detectedMime = finfo_file($finfo, $tmpPath);
finfo_close($finfo);

if (!in_array($detectedMime, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only MP4, WebM, OGG, MOV, AVI allowed.']);
    exit();
}

// Max 100 MB
$maxSize = 100 * 1024 * 1024;
if ($fileSize > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 100MB.']);
    exit();
}

// --- Check: user has not already submitted for this challenge ---
$conn = getDB();

$stmt = $conn->prepare(
    'SELECT id FROM challenge_submissions WHERE challenge_id = ? AND user_id = ?'
);
$stmt->bind_param('ii', $challengeId, $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'You have already submitted a video for this challenge.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// --- Save file to uploads/challenges/ ---
$uploadDir = __DIR__ . '/../uploads/challenges/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename: handle_timestamp_random.ext
$ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$safeHandle   = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace('@', '', $handle));
$newFilename  = $safeHandle . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destination  = $uploadDir . $newFilename;

if (!move_uploaded_file($tmpPath, $destination)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save the file. Please try again.']);
    $conn->close();
    exit();
}

// --- Insert submission into DB ---
$stmt = $conn->prepare(
    'INSERT INTO challenge_submissions
        (challenge_id, user_id, handle, video_filename, original_name, file_size, submitted_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->bind_param('iisssi', $challengeId, $userId, $handle, $newFilename, $originalName, $fileSize);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success'  => true,
        'message'  => 'Your video has been submitted successfully! Good luck 🎉',
        'filename' => $newFilename
    ]);
} else {
    // Rollback: delete uploaded file if DB insert fails
    unlink($destination);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}

$stmt->close();
$conn->close();
?>
