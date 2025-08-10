<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Danh sách trạng thái hợp lệ
$validStatuses = ['not_contacted', 'contacted', 'interested', 'no_need', 'unknown'];

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = isset($_POST['status']) ? strtolower($_POST['status']) : 'unknown';

// Kiểm tra trạng thái hợp lệ
if (!in_array($status, $validStatuses)) {
    $status = 'unknown';
}

if ($id <= 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$sql = "UPDATE users SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated', 'status' => $status]);
} else {
    echo json_encode(['error' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>