<?php
// api/get_leaderboard.php — Fetch leaderboard for the active challenge

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$conn = getDB();

// Get the active challenge
$challengeResult = $conn->query(
    "SELECT id, title, description FROM weekly_challenges WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
);

if ($challengeResult->num_rows === 0) {
    echo json_encode(['success' => true, 'challenge' => null, 'leaderboard' => []]);
    $conn->close();
    exit();
}

$challenge = $challengeResult->fetch_assoc();

// Get approved submissions ranked by points, then by submitted_at (earlier = higher)
$stmt = $conn->prepare(
    "SELECT cs.id, cs.handle, cs.points, cs.submitted_at, cs.video_filename
     FROM challenge_submissions cs
     WHERE cs.challenge_id = ? AND cs.status = 'approved'
     ORDER BY cs.points DESC, cs.submitted_at ASC
     LIMIT 10"
);
$stmt->bind_param('i', $challenge['id']);
$stmt->execute();
$result = $stmt->get_result();

$leaderboard = [];
$rank = 1;
while ($row = $result->fetch_assoc()) {
    $row['rank'] = $rank++;
    $leaderboard[] = $row;
}

// Get total submission count (all statuses) for display
$countStmt = $conn->prepare(
    "SELECT COUNT(*) as total FROM challenge_submissions WHERE challenge_id = ?"
);
$countStmt->bind_param('i', $challenge['id']);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();

echo json_encode([
    'success'      => true,
    'challenge'    => $challenge,
    'leaderboard'  => $leaderboard,
    'total_submissions' => (int)$countResult['total']
]);

$stmt->close();
$countStmt->close();
$conn->close();
?>
