<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $year_of_birth = $_POST['year_of_birth'] ?? '';
    $facility = $_POST['facility'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($id) || empty($full_name) || empty($phone_number) || empty($year_of_birth) || empty($facility) || empty($email)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, year_of_birth = ?, facility = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $full_name, $phone_number, $year_of_birth, $facility, $email, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>