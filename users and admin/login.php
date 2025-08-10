<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_error = '';

$languages = [
    'en' => [
        'title' => 'Admin Login',
        'login_input_label' => 'Email or Phone Number',
        'password_label' => 'Password',
        'login_button' => 'Login',
        'error_invalid_credentials' => 'Invalid email or phone number',
        'error_invalid_password' => 'Invalid password'
    ],
    'vi' => [
        'title' => 'Đăng Nhập Quản Trị',
        'login_input_label' => 'Email hoặc Số Điện Thoại',
        'password_label' => 'Mật Khẩu',
        'login_button' => 'Đăng Nhập',
        'error_invalid_credentials' => 'Email hoặc số điện thoại không hợp lệ',
        'error_invalid_password' => 'Mật khẩu không đúng'
    ]
];

$selectedLang = isset($_POST['lang']) ? $_POST['lang'] : (isset($_GET['lang']) ? $_GET['lang'] : 'en');
if (!array_key_exists($selectedLang, $languages)) {
    $selectedLang = 'en';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_input'])) {
    $login_input = $_POST['login_input'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE Email = ? OR PhoneNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $login_input, $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['Password'])) {
            $_SESSION['admin_id'] = $admin['ID'];
            $_SESSION['admin_name'] = $admin['FullName'];
            $_SESSION['adminId'] = $admin['ID'];
            header('Location: users.php');
            exit();
        } else {
            $login_error = $languages[$selectedLang]['error_invalid_password'];
        }
    } else {
        $login_error = $languages[$selectedLang]['error_invalid_credentials'];
    }

    $stmt->close();
}

$conn->close();
?>

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
        <form id="language-form" method="POST" action="login.php">
            <select name="lang" onchange="this.form.submit()" class="bg-gray-800 text-gray-200 border border-gray-600 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="en" <?php echo $selectedLang === 'en' ? 'selected' : ''; ?>>English</option>
                <option value="vi" <?php echo $selectedLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
            </select>
        </form>
    </div>
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8 relative">
        <img src="https://sme.hust.edu.vn/wp-content/uploads/2022/02/Avatar-Facebook-trang.jpg" alt="Avatar" class="avatar-img">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6 mt-12"><?php echo $languages[$selectedLang]['title']; ?></h2>
        <?php if ($login_error): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-md mb-4 text-center"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" id="login-form">
            <input type="hidden" name="lang" value="<?php echo $selectedLang; ?>">
            <div class="mb-4">
                <label for="login_input" class="block text-gray-700 font-medium mb-2"><?php echo $languages[$selectedLang]['login_input_label']; ?></label>
                <input type="text" id="login_input" name="login_input" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2"><?php echo $languages[$selectedLang]['password_label']; ?></label>
                <input type="password" id="password" name="password" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md font-medium hover:bg-blue-700 transition duration-300"><?php echo $languages[$selectedLang]['login_button']; ?></button>
            <div class="mt-4 text-center">
                <a href="restore.php" class="text-blue-600 hover:underline"><?php echo $selectedLang === 'en' ? 'Forgot password?' : 'Quên mật khẩu?'; ?></a>
            </div>
        </form>
    </div>
</body>
</html>