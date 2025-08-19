<?php
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Kết nối thất bại']));
}

$id = $_POST['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM staff WHERE ID = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>