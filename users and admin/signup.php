<?php
// Kết nối đến CSDL
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "btec_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Xử lý ảnh đại diện
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $avatar_name = time() . "_" . basename($_FILES["avatar"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . $avatar_name;
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);
    } else {
        $avatar_name = "https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png";
    }

    // Chèn vào CSDL
    $sql = "INSERT INTO admin (FullName, Email, PhoneNumber, Password, Avatar)
            VALUES ('$fullName', '$email', '$phone', '$password', '$avatar_name')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Đăng ký thành công!'); window.location.href='signup.php';</script>";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký Admin</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="signup-container">
        <h2>Đăng ký tài khoản Admin</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="fullname" placeholder="Họ và tên" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Số điện thoại" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <label>Ảnh đại diện (tùy chọn):</label>
            <input type="file" name="avatar" accept="image/*">
            <button type="submit">Đăng ký</button>
        </form>
    </div>
    <script src="signup.js"></script>
</body>
</html>
