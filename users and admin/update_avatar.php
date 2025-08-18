<?php
session_start();

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['adminId']) || !isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'btec_db');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$adminId = intval($_SESSION['adminId']);
$selectedLang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['croppedImage'])) {
    $data = $_POST['croppedImage'];

    // Lấy dữ liệu base64
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // Lấy avatar hiện tại để xóa nếu cần
    $sql = "SELECT Avatar FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentAvatar = $result->num_rows > 0 ? $result->fetch_assoc()['Avatar'] : null;
    $stmt->close();

    // Lưu file ảnh mới
    $uploadDir = 'Uploads/admin/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = $uploadDir . 'avatar_' . $adminId . '_' . time() . '.jpg';

    if (file_put_contents($fileName, $data) !== false) {
        // Xóa file ảnh cũ nếu tồn tại và không phải ảnh mặc định
        if ($currentAvatar && file_exists($currentAvatar) && strpos($currentAvatar, 'default') === false) {
            unlink($currentAvatar);
        }

        // Cập nhật đường dẫn ảnh vào cơ sở dữ liệu
        $fileNameForDB = $conn->real_escape_string($fileName);
        $sql = "UPDATE admin SET Avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $fileNameForDB, $adminId);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: profile.php?lang=$selectedLang");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            // Thông báo lỗi nếu cập nhật cơ sở dữ liệu thất bại
            header("Location: profile.php?lang=$selectedLang&error=db_update_failed");
            exit();
        }
    } else {
        $conn->close();
        // Thông báo lỗi nếu lưu file thất bại
        header("Location: profile.php?lang=$selectedLang&error=file_save_failed");
        exit();
    }
}

$conn->close();
header("Location: profile.php?lang=$selectedLang");
exit();
?>