<?php
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Kết nối thất bại']));
}

$id = $_POST['id'] ?? 0;
$full_name = $_POST['FullName'] ?? '';
$email = $_POST['Email'] ?? '';
$phone_number = $_POST['PhoneNumber'] ?? '';
$role = $_POST['role'] ?? 'admin';

$stmt = $conn->prepare("UPDATE admin SET FullName = ?, Email = ?, PhoneNumber = ?, role = ? WHERE ID = ?");
$stmt->bind_param("ssssi", $full_name, $email, $phone_number, $role, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>