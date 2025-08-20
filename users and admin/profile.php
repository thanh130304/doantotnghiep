<?php
session_start();

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$role = $_SESSION['role'];

$conn = new mysqli('localhost', 'root', '', 'btec_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin staff theo id
$sql = "SELECT FullName, PhoneNumber, Email FROM staff WHERE ID = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();

$fixedAvatar = 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://readdy.ai/api/search-image?query=modern%20university%20campus%20with%20students%20studying%20in%20a%20bright%2C%20contemporary%20learning%20environment.%20The%20scene%20shows%20a%20mix%20of%20indoor%20and%20outdoor%20spaces%20with%20glass%20walls%2C%20modern%20architecture%2C%20and%20natural%20light.%20Students%20are%20engaged%20in%20collaborative%20work.%20The%20image%20has%20a%20clean%2C%20professional%20aesthetic%20with%20a%20light%2C%20airy%20atmosphere&width=1920&height=1080&seq=1&orientation=landscape');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Arial', sans-serif;
        }
        #backBtn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1050;
        }
        .avatar-wrapper {
            width: 100px;
            height: 100px;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }
        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card {
            border-color: #007bff;
            background-color: rgba(255, 255, 255, 0.9);
        }
        @media (max-width: 768px) {
            .card-body {
                flex-direction: column;
                align-items: center;
            }
            .flex-grow-1 {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Nút Back -->
    <a href="<?php echo ($role === 'manager') ? 'manage.php' : 'users.php'; ?>" id="backBtn" class="btn btn-danger">
        &larr; Back
    </a>

    <div class="container py-5 mt-5">
        <div class="card shadow">
            <div class="card-header">
                <h3 class="mb-0">Staff Profile</h3>
            </div>
            <div class="card-body d-flex align-items-start">
                <!-- Thông tin căn trái -->
                <div class="flex-grow-1 pe-4">
                    <div class="mb-3 mt-1">
                        <label class="form-label fw-bold">Full Name:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['FullName']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['PhoneNumber']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['Email']); ?></p>
                    </div>
                </div>

                <!-- Avatar bên phải -->
                <div class="avatar-wrapper">
                    <img src="<?php echo htmlspecialchars($fixedAvatar); ?>" alt="Avatar" class="rounded-circle">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>