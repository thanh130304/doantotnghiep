<?php
session_start();

// Kiểm tra đã đăng nhập chưa
if (!isset($_SESSION['adminId'])) {
    header("Location: login.php");
    exit();
}

$adminId = intval($_SESSION['adminId']);

$conn = new mysqli('localhost', 'root', '', 'btec_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin admin theo id
$sql = "SELECT FullName, PhoneNumber, Email, Avatar FROM admin WHERE id = $adminId LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    $conn->close();
    header("Location: login.php");
    exit();
}

$conn->close();

$defaultAvatar = 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png';
$avatar = !empty($data['Avatar']) ? $data['Avatar'] : $defaultAvatar;

// Language data
$languages = [
    'en' => [
        'title' => 'Admin Profile',
        'full_name' => 'Full Name',
        'phone_number' => 'Phone Number',
        'email' => 'Email',
        'adjust_avatar' => 'Adjust Avatar',
        'cancel' => 'Cancel',
        'save' => 'Save'
    ],
    'vi' => [
        'title' => 'Hồ Sơ Quản Trị',
        'full_name' => 'Họ và Tên',
        'phone_number' => 'Số Điện Thoại',
        'email' => 'Email',
        'adjust_avatar' => 'Chỉnh Sửa Ảnh Đại Diện',
        'cancel' => 'Hủy',
        'save' => 'Lưu'
    ]
];

$selectedLang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
?>

<!DOCTYPE html>
<html lang="<?php echo $selectedLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $languages[$selectedLang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet">
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
            cursor: pointer;
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
            transition: 0.3s ease;
        }
        .avatar-wrapper:hover img {
            filter: brightness(0.8);
        }
        .overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.35);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            font-size: 12px;
            transition: opacity 0.3s;
            border-radius: 50%;
            user-select: none;
        }
        .avatar-wrapper:hover .overlay {
            opacity: 1;
        }
        #cropImage {
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }
        .language-select {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1050;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card {
            border-color: #007bff;
            background-color: rgba(255, 255, 255, 0.9); /* Nền card trong suốt nhẹ để thấy background */
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
    <a href="users.php?lang=<?php echo $selectedLang; ?>" id="backBtn" class="btn btn-danger">
        &larr; Back
    </a>

    <!-- Dropdown chọn ngôn ngữ -->
    <div class="language-select">
        <select id="language-select" class="form-select" style="width: auto;" onchange="changeLanguage()">
            <option value="en" <?php echo $selectedLang === 'en' ? 'selected' : ''; ?>>English</option>
            <option value="vi" <?php echo $selectedLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
        </select>
    </div>

    <div class="container py-5 mt-5">
        <div class="card shadow">
            <div class="card-header">
                <h3 class="mb-0"><?php echo $languages[$selectedLang]['title']; ?></h3>
            </div>
            <div class="card-body d-flex align-items-start">
                <!-- Thông tin căn trái -->
                <div class="flex-grow-1 pe-4">
                    <div class="mb-3 mt-1">
                        <label class="form-label fw-bold"><?php echo $languages[$selectedLang]['full_name']; ?>:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['FullName']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo $languages[$selectedLang]['phone_number']; ?>:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['PhoneNumber']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo $languages[$selectedLang]['email']; ?>:</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($data['Email']); ?></p>
                    </div>
                </div>

                <!-- Avatar bên phải -->
                <form id="avatarForm" method="POST" action="update_avatar.php" class="m-0">
                    <input type="file" id="avatarInput" accept="image/*" hidden>
                    <input type="hidden" name="croppedImage" id="croppedImageInput">
                    <div class="avatar-wrapper" onclick="document.getElementById('avatarInput').click()">
                        <img src="<?php echo $avatar; ?>" alt="Avatar" class="rounded-circle" id="avatarDisplay">
                        <div class="overlay"><?php echo $languages[$selectedLang]['adjust_avatar']; ?></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal crop ảnh -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel"><?php echo $languages[$selectedLang]['adjust_avatar']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="cropImage">
                </div>
                <div class="modal-footer">
                    <button type="button" id="cropCancelBtn" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" id="cropSaveBtn" class="btn btn-primary"><?php echo $languages[$selectedLang]['save']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
    <script>
        function changeLanguage() {
            const lang = document.getElementById('language-select').value;
            window.location.href = `?lang=${lang}`;
        }

        let cropper;
        const avatarInput = document.getElementById('avatarInput');
        const cropImage = document.getElementById('cropImage');
        const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
        const croppedImageInput = document.getElementById('croppedImageInput');
        const avatarForm = document.getElementById('avatarForm');

        avatarInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                const file = e.target.files[0];
                const url = URL.createObjectURL(file);
                cropImage.src = url;

                cropModal.show();

                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                });
            }
        });

        document.getElementById('cropSaveBtn').addEventListener('click', () => {
            if (cropper) {
                cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    fillColor: '#fff',
                }).toBlob((blob) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        croppedImageInput.value = reader.result;
                        cropModal.hide();
                        avatarForm.submit();
                    };
                }, 'image/jpeg', 0.9);
            }
        });

        document.getElementById('cropCancelBtn').addEventListener('click', () => {
            avatarInput.value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
    </script>
</body>
</html>