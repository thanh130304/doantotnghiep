<?php
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Kết nối thất bại']));
}

$full_name = $_POST['FullName'] ?? '';
$email = $_POST['Email'] ?? '';
$phone_number = $_POST['PhoneNumber'] ?? '';
$password = password_hash($_POST['Password'] ?? '', PASSWORD_DEFAULT);
$role = $_POST['role'] ?? 'admin';

$stmt = $conn->prepare("INSERT INTO admin (FullName, Email, PhoneNumber, Password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $full_name, $email, $phone_number, $password, $role);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>