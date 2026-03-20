<?php
// api/review_submission.php — Admin: approve or reject a submission + award points

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$data   = json_decode(file_get_contents('php://input'), true);
$id     = isset($data['id'])     ? (int)$data['id']          : 0;
$status = isset($data['status']) ? trim($data['status'])      : '';
$points = isset($data['points']) ? (int)$data['points']       : 0;

if (!$id || !in_array($status, ['approved', 'rejected', 'pending'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid submission ID or status.']);
    exit();
}

$conn = getDB();

$stmt = $conn->prepare(
    'UPDATE challenge_submissions SET status = ?, points = ? WHERE id = ?'
);
$stmt->bind_param('sii', $status, $points, $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => "Submission $status successfully."]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Submission not found.']);
}

$stmt->close();
$conn->close();
?>
