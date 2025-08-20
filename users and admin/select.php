<!-- <?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập và có vai trò là manager hay không
// if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['role'] !== 'manager') {
//     header("Location: login.php");
//     exit;
// }

// $selectedLang = isset($_GET['lang']) ? $_GET['lang'] : 'vi';

// $languages = [
//     'en' => [
//         'title' => 'Select Management Area',
//         'select_area' => 'Select Management Area',
//         'staff' => 'Staff',
//         'student' => 'Student',
//         'please_select' => 'Please select an area to manage.',
//     ],
//     'vi' => [
//         'title' => 'Chọn Vùng Quản Lý',
//         'select_area' => 'Chọn Vùng Quản Lý',
//         'staff' => 'Nhân Viên',
//         'student' => 'Sinh Viên',
//         'please_select' => 'Vui lòng chọn một vùng để quản lý.',
//     ]
// ];
// ?>

<!DOCTYPE html>
<html lang="<?php echo $selectedLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $languages[$selectedLang]['title']; ?></title>
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
        <form id="language-form" method="GET" action="select.php">
            <select name="lang" onchange="this.form.submit()" class="bg-gray-800 text-gray-200 border border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="en" <?php echo $selectedLang === 'en' ? 'selected' : ''; ?>>English</option>
                <option value="vi" <?php echo $selectedLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
            </select>
        </form>
    </div>
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8 relative">
        <img src="https://sme.hust.edu.vn/wp-content/uploads/2022/02/Avatar-Facebook-trang.jpg" alt="Avatar" class="avatar-img">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6 mt-12"><?php echo $languages[$selectedLang]['select_area']; ?></h2>
        <div class="flex flex-col space-y-4">
            <button type="button" onclick="window.location.href='manage.php'" class="w-full bg-blue-600 text-white p-3 rounded-md font-medium hover:bg-blue-700 transition duration-300"><?php echo $languages[$selectedLang]['staff']; ?></button>
            <button type="button" onclick="window.location.href='users.php'" class="w-full bg-green-600 text-white p-3 rounded-md font-medium hover:bg-green-700 transition duration-300"><?php echo $languages[$selectedLang]['student']; ?></button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Có thể thêm logic JavaScript nếu cần, nhưng hiện tại chỉ cần chuyển hướng đơn giản
        });
    </script>
</body>
</html> -->