<?php
// api/get_submissions.php — Admin: get all submissions for active challenge

require_once '../config/headers.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$conn = getDB();

$result = $conn->query(
    "SELECT cs.id, cs.handle, cs.original_name, cs.video_filename,
            cs.file_size, cs.points, cs.status, cs.submitted_at,
            wc.title AS challenge_title
     FROM challenge_submissions cs
     JOIN weekly_challenges wc ON cs.challenge_id = wc.id
     ORDER BY cs.submitted_at DESC"
);

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $row['file_size_mb'] = round($row['file_size'] / (1024 * 1024), 2);
    $submissions[] = $row;
}

echo json_encode([
    'success'     => true,
    'submissions' => $submissions,
    'total'       => count($submissions)
]);

$conn->close();
?>
