<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$required = ['fullName', 'regNo', 'email', 'department', 'level', 'password'];
foreach ($required as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

$fullName = $data['fullName'];
$regNo = $data['regNo'];
$email = $data['email'];
$department = $data['department'];
$level = $data['level'];
$password = $data['password'];

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

$conn = getDBConnection();

// Check if reg_no exists
$check = $conn->prepare("SELECT id FROM students WHERE reg_no = ? OR email = ?");
$check->bind_param("ss", $regNo, $email);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Registration number or email already exists']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insert new student
$stmt = $conn->prepare("INSERT INTO students (reg_no, full_name, email, department, level, password) VALUES (?, ?, ?, ?, ?, MD5(?))");
$stmt->bind_param("ssssss", $regNo, $fullName, $email, $department, $level, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>