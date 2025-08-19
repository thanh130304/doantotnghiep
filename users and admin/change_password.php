<?php
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => $conn->connect_error]);
    exit;
}

$email_or_phone = $_POST['email_or_phone'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($email_or_phone) || empty($new_password)) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Determine if input is email or phone number based on presence of '@'
if (strpos($email_or_phone, '@') !== false) {
    $sql = "UPDATE admin SET Password = ? WHERE Email = ?";
} else {
    $sql = "UPDATE admin SET Password = ? WHERE PhoneNumber = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $email_or_phone);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No matching record found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>