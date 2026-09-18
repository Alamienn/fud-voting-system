<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['regNo']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

$regNo = $data['regNo'];
$password = $data['password'];

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT id, reg_no, full_name, email, department, level, has_voted FROM students WHERE reg_no = ? AND password = MD5(?)");
$stmt->bind_param("ss", $regNo, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid registration number or password'
    ]);
}

$stmt->close();
$conn->close();
?>