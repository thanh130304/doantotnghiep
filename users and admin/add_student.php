<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$full_name = $_POST['full_name'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$year_of_birth = $_POST['year_of_birth'] ?? '';
$facility = $_POST['facility'] ?? '';
$email = $_POST['email'] ?? '';
$status = $_POST['status'] ?? 'unknown';

if (empty($full_name) || empty($phone_number) || empty($year_of_birth) || empty($facility) || empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (full_name, phone_number, year_of_birth, facility, email, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $full_name, $phone_number, $year_of_birth, $facility, $email, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Lỗi khi thêm học viên: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>