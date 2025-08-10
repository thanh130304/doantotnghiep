<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'btec_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminId = 1;

if (isset($_POST['croppedImage'])) {
    $data = $_POST['croppedImage'];

    // Lấy dữ liệu base64
    list($type, $data) = explode(';', $data);
    list(, $data)      = explode(',', $data);
    $data = base64_decode($data);

    $uploadDir = 'uploads/admin/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = $uploadDir . 'avatar_' . time() . '.jpg';

    file_put_contents($fileName, $data);

    // Cập nhật DB đường dẫn ảnh (đường dẫn tương đối)
    $fileNameForDB = $conn->real_escape_string($fileName);
    $sql = "UPDATE admin SET Avatar = '$fileNameForDB' WHERE id = $adminId";
    $conn->query($sql);
}

$conn->close();
header("Location: profile.php");
exit();
?>
