<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['candidate_id']) || !isset($data['position_id']) || !isset($data['election_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing vote data']);
    exit;
}

$student_id = $_SESSION['student_id'];
$candidate_id = $data['candidate_id'];
$position_id = $data['position_id'];
$election_id = $data['election_id'];

$conn = getDBConnection();

// Check if student already voted for this position in this election
$check = $conn->prepare("SELECT id FROM votes WHERE student_id = ? AND position_id = ? AND election_id = ?");
$check->bind_param("iii", $student_id, $position_id, $election_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already voted for this position']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Check if election is active
$electionCheck = $conn->prepare("SELECT status FROM elections WHERE id = ? AND status = 'active'");
$electionCheck->bind_param("i", $election_id);
$electionCheck->execute();
$electionResult = $electionCheck->get_result();

if ($electionResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Election is not active']);
    $electionCheck->close();
    $conn->close();
    exit;
}
$electionCheck->close();

// Record vote
$stmt = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position_id, election_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $student_id, $candidate_id, $position_id, $election_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vote recorded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to record vote: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>