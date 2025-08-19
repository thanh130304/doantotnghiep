<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Kết nối thất bại']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = $_POST['login_input'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT ID, Email, Password, role FROM staff WHERE Email = ? OR PhoneNumber = ?");
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['role'] = $user['role'];
            // Chuyển hướng dựa trên role
            $redirect = ($user['role'] === 'manager') ? 'manage.php' : 'users.php';
            echo json_encode(['success' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Mật khẩu không đúng']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Email hoặc số điện thoại không tồn tại']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        input:focus, select:focus {
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
        }

        button:hover {
            transform: translateY(-2px);
        }

        .avatar-img {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 640px) {
            .max-w-md {
                margin: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="absolute top-4 right-4">
        <form id="language-form" method="POST" action="login.php">
            <select name="lang" onchange="this.form.submit()" class="bg-gray-800 text-gray-200 border border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="en">English</option>
                <option value="vi">Tiếng Việt</option>
            </select>
        </form>
    </div>
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8 relative">
        <img src="https://sme.hust.edu.vn/wp-content/uploads/2022/02/Avatar-Facebook-trang.jpg" alt="Avatar" class="avatar-img">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6 mt-12">Đăng nhập</h2>
        <form method="POST" action="login.php" id="login-form">
            <div class="mb-4">
                <label for="login_input" class="block text-gray-700 font-medium mb-2">Email hoặc Số điện thoại</label>
                <input type="text" class="form-control w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="login_input" name="login_input" placeholder="Nhập email hoặc số điện thoại" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
                <input type="password" class="form-control w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md font-medium hover:bg-blue-700 transition duration-300">Đăng nhập</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('login_input', document.getElementById('login_input').value);
            formData.append('password', document.getElementById('password').value);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert('Đăng nhập thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi đăng nhập.');
            });
        });
    </script>
</body>
</html>