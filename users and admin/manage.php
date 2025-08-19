<?php
session_start(); // Bắt đầu session để kiểm tra trạng thái đăng nhập

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT ID, FullName, Email, PhoneNumber, Password, role FROM staff";
$result = $conn->query($sql);

// Lọc và sắp xếp
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$whereClauses = [];
$params = [];
if (!empty($search)) {
    $whereClauses[] = "(FullName LIKE ? OR Email LIKE ? OR PhoneNumber LIKE ?)";
    $searchParam = "%$search%";
    $params = array_fill(0, 3, $searchParam);
}
if (!empty($roleFilter) && $roleFilter !== 'all') {
    $whereClauses[] = "role = ?";
    $params[] = $roleFilter;
}

$sql = "SELECT ID, FullName, Email, PhoneNumber, Password, role FROM staff";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$orderBy = '';
switch ($sort) {
    case 'name_asc':
        $orderBy = "ORDER BY FullName ASC";
        break;
    case 'name_desc':
        $orderBy = "ORDER BY FullName DESC";
        break;
    default:
        $orderBy = "";
}
if (!empty($orderBy)) {
    $sql .= " " . $orderBy;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$selectedLang = isset($_GET['lang']) ? $_GET['lang'] : 'vi';

$languages = [
    'en' => [
        'title' => 'Staff Management List',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'phone_number' => 'Phone Number',
        'password' => 'Password',
        'role' => 'Role',
        'no_records' => 'No records found',
        'add_staff' => 'Add Staff',
        'edit_staff' => 'Edit Staff',
        'signup_staff' => 'Sign Up Staff',
        'confirm_delete' => 'Are you sure you want to delete this staff?',
        'yes' => 'Yes',
        'no' => 'No',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'update' => 'Update',
        'search_placeholder' => 'Search staff...',
        'all_roles' => 'All roles',
        'sort_by' => 'Sort by',
        'name_asc' => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'manage_staff' => 'Manage Staff',
        'logout' => 'Logout',
        'login' => 'Login',
        'signup' => 'Sign Up',
        'actions' => 'Actions',
        'please_login' => 'Please login before accessing',
        'change_password' => 'Change Password',
        'email_or_phone' => 'Email or Phone Number',
        'new_password' => 'New Password',
        're_enter_new_password' => 'Re-enter New Password'
    ],
    'vi' => [
        'title' => 'Danh Sách Quản Lý Nhân Viên',
        'full_name' => 'Họ và Tên',
        'email' => 'Email',
        'phone_number' => 'Số Điện Thoại',
        'password' => 'Mật khẩu',
        'role' => 'Vai Trò',
        'no_records' => 'Không tìm thấy bản ghi nào',
        'add_staff' => 'Thêm Nhân Viên',
        'edit_staff' => 'Chỉnh Sửa Nhân Viên',
        'signup_staff' => 'Đăng ký Nhân Viên',
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa nhân viên này?',
        'yes' => 'Có',
        'no' => 'Không',
        'cancel' => 'Hủy',
        'save' => 'Lưu',
        'update' => 'Cập nhật',
        'search_placeholder' => 'Tìm kiếm nhân viên...',
        'all_roles' => 'Tất cả vai trò',
        'sort_by' => 'Sắp xếp theo',
        'name_asc' => 'Họ tên A-Z',
        'name_desc' => 'Họ tên Z-A',
        'manage_staff' => 'Quản lý Nhân Viên',
        'logout' => 'Đăng xuất',
        'login' => 'Đăng nhập',
        'signup' => 'Đăng ký',
        'actions' => 'Hành động',
        'please_login' => 'Vui lòng đăng nhập trước khi truy cập',
        'change_password' => 'Thay đổi Mật khẩu',
        'email_or_phone' => 'Email hoặc Số Điện Thoại',
        'new_password' => 'Mật khẩu Mới',
        're_enter_new_password' => 'Nhập Lại Mật khẩu Mới'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $selectedLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $languages[$selectedLang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .table-container {
            max-height: 320px;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #008000;
            color: #ffffff;
        }
        .table-container th, .table-container td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .btn-edit {
            background-color: #28a745;
            color: #ffffff;
            border: none;
            margin-right: 5px;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
        }
        .sidebar-logo {
            width: 100%;
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .sidebar .nav-item {
            width: 100%;
        }
        .sidebar .nav-link {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="text-center py-4">
                    <img src="https://daihoc.fpt.edu.vn/wp-content/uploads/2024/11/Logo-Btec.webp" alt="FPT Logo" class="sidebar-logo">
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-lang-key="manage_staff">
                            <i class="fas fa-users me-2"></i><?php echo $languages[$selectedLang]['manage_staff']; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'logout.php' : 'login.php'; ?>" data-lang-key="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'logout' : 'login'; ?>">
                            <i class="fas fa-sign-<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'out' : 'in'; ?>-alt me-2"></i><?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? $languages[$selectedLang]['logout'] : $languages[$selectedLang]['login']; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupStaffModal" data-lang-key="signup">
                            <i class="fas fa-user-plus me-2"></i><?php echo $languages[$selectedLang]['signup']; ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $languages[$selectedLang]['title']; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <select id="language-select" class="form-select me-2" style="width: auto;" onchange="updateLanguage(this.value)">
                            <option value="en" <?php echo $selectedLang === 'en' ? 'selected' : ''; ?>>English</option>
                            <option value="vi" <?php echo $selectedLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
                        </select>
                    </div>
                </div>

                <!-- Search and filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="<?php echo $languages[$selectedLang]['search_placeholder']; ?>" value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="roleFilter" onchange="applyFilters()">
                                    <option value="all" <?php echo empty($roleFilter) ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['all_roles']; ?></option>
                                    <option value="manager" <?php echo $roleFilter === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                    <option value="staff" <?php echo $roleFilter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="sortFilter" onchange="applyFilters()">
                                    <option value="" <?php echo empty($sort) ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['sort_by']; ?></option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['name_asc']; ?></option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['name_desc']; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-container">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th data-lang-key="full_name"><?php echo $languages[$selectedLang]['full_name']; ?></th>
                                            <th data-lang-key="email"><?php echo $languages[$selectedLang]['email']; ?></th>
                                            <th data-lang-key="phone_number"><?php echo $languages[$selectedLang]['phone_number']; ?></th>
                                            <th data-lang-key="password"><?php echo $languages[$selectedLang]['password']; ?></th>
                                            <th data-lang-key="role"><?php echo $languages[$selectedLang]['role']; ?></th>
                                            <th data-lang-key="actions"><?php echo $languages[$selectedLang]['actions']; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr data-id='" . $row['ID'] . "'>
                                                    <td>" . $row['ID'] . "</td>
                                                    <td><span class='full-name'>" . htmlspecialchars($row['FullName']) . "</span></td>
                                                    <td><span class='email'>" . htmlspecialchars($row['Email']) . "</span></td>
                                                    <td><span class='phone-number'>" . htmlspecialchars($row['PhoneNumber']) . "</span></td>
                                                    <td><span class='password'>" . htmlspecialchars($row['Password']) . "</span></td>
                                                    <td><span class='role'>" . htmlspecialchars($row['role']) . "</span></td>
                                                    <td>
                                                        <button type='button' class='btn btn-sm btn-edit' data-bs-toggle='modal' data-bs-target='#editStaffModal' onclick=\"loadStaffData('" . $row['ID'] . "', '" . htmlspecialchars($row['FullName']) . "', '" . htmlspecialchars($row['Email']) . "', '" . htmlspecialchars($row['PhoneNumber']) . "', '" . htmlspecialchars($row['Password']) . "', '" . htmlspecialchars($row['role']) . "')\">
                                                            <i class='fas fa-edit'></i>
                                                        </button>
                                                        <button type='button' class='btn btn-sm btn-delete' data-bs-toggle='modal' data-bs-target='#deleteConfirmModal' onclick=\"setDeleteId('" . $row['ID'] . "')\">
                                                            <i class='fas fa-trash'></i>
                                                        </button>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center text-muted'>" . $languages[$selectedLang]['no_records'] . "</td></tr>";
                                        }
                                        $stmt->close();
                                        $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="button-container d-flex mt-3" id="button-container">
                                <div>
                                    <button type="button" id="change-password-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><?php echo $languages[$selectedLang]['change_password']; ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel"><?php echo $languages[$selectedLang]['add_staff']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <div class="mb-3">
                            <label for="staffName" class="form-label"><?php echo $languages[$selectedLang]['full_name']; ?></label>
                            <input type="text" class="form-control" id="staffName" placeholder="<?php echo $languages[$selectedLang]['full_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffEmail" class="form-label"><?php echo $languages[$selectedLang]['email']; ?></label>
                            <input type="email" class="form-control" id="staffEmail" placeholder="<?php echo $languages[$selectedLang]['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffPhone" class="form-label"><?php echo $languages[$selectedLang]['phone_number']; ?></label>
                            <input type="tel" class="form-control" id="staffPhone" placeholder="<?php echo $languages[$selectedLang]['phone_number']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffPassword" class="form-label"><?php echo $languages[$selectedLang]['password']; ?></label>
                            <input type="password" class="form-control" id="staffPassword" placeholder="<?php echo $languages[$selectedLang]['password']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffRole" class="form-label"><?php echo $languages[$selectedLang]['role']; ?></label>
                            <select class="form-select" id="staffRole" required>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="addStaff()"><?php echo $languages[$selectedLang]['save']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel"><?php echo $languages[$selectedLang]['edit_staff']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm">
                        <input type="hidden" id="editStaffId">
                        <div class="mb-3">
                            <label for="editStaffName" class="form-label"><?php echo $languages[$selectedLang]['full_name']; ?></label>
                            <input type="text" class="form-control" id="editStaffName" placeholder="<?php echo $languages[$selectedLang]['full_name']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffEmail" class="form-label"><?php echo $languages[$selectedLang]['email']; ?></label>
                            <input type="email" class="form-control" id="editStaffEmail" placeholder="<?php echo $languages[$selectedLang]['email']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffPhone" class="form-label"><?php echo $languages[$selectedLang]['phone_number']; ?></label>
                            <input type="tel" class="form-control" id="editStaffPhone" placeholder="<?php echo $languages[$selectedLang]['phone_number']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffPassword" class="form-label"><?php echo $languages[$selectedLang]['password']; ?></label>
                            <input type="text" class="form-control" id="editStaffPassword" placeholder="<?php echo $languages[$selectedLang]['password']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editStaffRole" class="form-label"><?php echo $languages[$selectedLang]['role']; ?></label>
                            <select class="form-select" id="editStaffRole">
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="updateStaff()"><?php echo $languages[$selectedLang]['update']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Up Staff Modal -->
    <div class="modal fade" id="signupStaffModal" tabindex="-1" aria-labelledby="signupStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupStaffModalLabel"><?php echo $languages[$selectedLang]['signup_staff']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupStaffForm">
                        <div class="mb-3">
                            <label for="signupStaffName" class="form-label"><?php echo $languages[$selectedLang]['full_name']; ?></label>
                            <input type="text" class="form-control" id="signupStaffName" placeholder="<?php echo $languages[$selectedLang]['full_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffEmail" class="form-label"><?php echo $languages[$selectedLang]['email']; ?></label>
                            <input type="email" class="form-control" id="signupStaffEmail" placeholder="<?php echo $languages[$selectedLang]['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffPhone" class="form-label"><?php echo $languages[$selectedLang]['phone_number']; ?></label>
                            <input type="tel" class="form-control" id="signupStaffPhone" placeholder="<?php echo $languages[$selectedLang]['phone_number']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffPassword" class="form-label"><?php echo $languages[$selectedLang]['password']; ?></label>
                            <input type="password" class="form-control" id="signupStaffPassword" placeholder="<?php echo $languages[$selectedLang]['password']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffRole" class="form-label"><?php echo $languages[$selectedLang]['role']; ?></label>
                            <select class="form-select" id="signupStaffRole" required>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="signupStaff()"><?php echo $languages[$selectedLang]['save']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel"><?php echo $languages[$selectedLang]['change_password']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="emailOrPhone" class="form-label"><?php echo $languages[$selectedLang]['email_or_phone']; ?></label>
                            <input type="text" class="form-control" id="emailOrPhone" placeholder="<?php echo $languages[$selectedLang]['email_or_phone']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label"><?php echo $languages[$selectedLang]['new_password']; ?></label>
                            <input type="password" class="form-control" id="newPassword" placeholder="<?php echo $languages[$selectedLang]['new_password']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label"><?php echo $languages[$selectedLang]['re_enter_new_password']; ?></label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="<?php echo $languages[$selectedLang]['re_enter_new_password']; ?>" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="changePassword()"><?php echo $languages[$selectedLang]['update']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel"><?php echo $languages[$selectedLang]['confirm_delete']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $languages[$selectedLang]['confirm_delete']; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['no']; ?></button>
                    <button type="button" class="btn btn-danger" onclick="deleteStaff()"><?php echo $languages[$selectedLang]['yes']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const languages = <?php echo json_encode($languages); ?>;
        let deleteStaffId = null;
        const isLoggedIn = <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false'; ?>;

        function updateLanguage(lang) {
            document.querySelector('h1.h2').textContent = languages[lang].title;
            document.querySelector('th[data-lang-key="full_name"]').textContent = languages[lang].full_name;
            document.querySelector('th[data-lang-key="email"]').textContent = languages[lang].email;
            document.querySelector('th[data-lang-key="phone_number"]').textContent = languages[lang].phone_number;
            document.querySelector('th[data-lang-key="password"]').textContent = languages[lang].password;
            document.querySelector('th[data-lang-key="role"]').textContent = languages[lang].role;
            document.querySelector('th[data-lang-key="actions"]').textContent = languages[lang].actions;
            document.getElementById('change-password-btn').textContent = languages[lang].change_password;
            document.querySelector('#searchInput').placeholder = languages[lang].search_placeholder;
            document.querySelector('#roleFilter option[value="all"]').textContent = languages[lang].all_roles;
            document.querySelector('#roleFilter option[value="manager"]').textContent = 'Manager';
            document.querySelector('#roleFilter option[value="staff"]').textContent = 'Staff';
            document.querySelector('#sortFilter option[value=""]').textContent = languages[lang].sort_by;
            document.querySelector('#sortFilter option[value="name_asc"]').textContent = languages[lang].name_asc;
            document.querySelector('#sortFilter option[value="name_desc"]').textContent = languages[lang].name_desc;

            document.querySelector('a[data-lang-key="manage_staff"]').innerHTML = `<i class="fas fa-users me-2"></i>${languages[lang].manage_staff}`;
            const loginLogoutLink = document.querySelector('a[data-lang-key="login"], a[data-lang-key="logout"]');
            if (isLoggedIn) {
                loginLogoutLink.setAttribute('data-lang-key', 'logout');
                loginLogoutLink.innerHTML = `<i class="fas fa-sign-out-alt me-2"></i>${languages[lang].logout}`;
                loginLogoutLink.href = 'logout.php';
            } else {
                loginLogoutLink.setAttribute('data-lang-key', 'login');
                loginLogoutLink.innerHTML = `<i class="fas fa-sign-in-alt me-2"></i>${languages[lang].login}`;
                loginLogoutLink.href = 'login.php';
            }
            document.querySelector('a[data-lang-key="signup"]').innerHTML = `<i class="fas fa-user-plus me-2"></i>${languages[lang].signup}`;

            const addModal = document.getElementById('addStaffModal');
            if (addModal) {
                addModal.querySelector('#addStaffModalLabel').textContent = languages[lang].add_staff;
                addModal.querySelector('label[for="staffName"]').textContent = languages[lang].full_name;
                addModal.querySelector('#staffName').placeholder = languages[lang].full_name;
                addModal.querySelector('label[for="staffEmail"]').textContent = languages[lang].email;
                addModal.querySelector('#staffEmail').placeholder = languages[lang].email;
                addModal.querySelector('label[for="staffPhone"]').textContent = languages[lang].phone_number;
                addModal.querySelector('#staffPhone').placeholder = languages[lang].phone_number;
                addModal.querySelector('label[for="staffPassword"]').textContent = languages[lang].password;
                addModal.querySelector('#staffPassword').placeholder = languages[lang].password;
                addModal.querySelector('label[for="staffRole"]').textContent = languages[lang].role;
                addModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                addModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].save;
            }

            const editModal = document.getElementById('editStaffModal');
            if (editModal) {
                editModal.querySelector('#editStaffModalLabel').textContent = languages[lang].edit_staff;
                editModal.querySelector('label[for="editStaffName"]').textContent = languages[lang].full_name;
                editModal.querySelector('#editStaffName').placeholder = languages[lang].full_name;
                editModal.querySelector('label[for="editStaffEmail"]').textContent = languages[lang].email;
                editModal.querySelector('#editStaffEmail').placeholder = languages[lang].email;
                editModal.querySelector('label[for="editStaffPhone"]').textContent = languages[lang].phone_number;
                editModal.querySelector('#editStaffPhone').placeholder = languages[lang].phone_number;
                editModal.querySelector('label[for="editStaffPassword"]').textContent = languages[lang].password;
                editModal.querySelector('#editStaffPassword').placeholder = languages[lang].password;
                editModal.querySelector('label[for="editStaffRole"]').textContent = languages[lang].role;
                editModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                editModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].update;
            }

            const signupModal = document.getElementById('signupStaffModal');
            if (signupModal) {
                signupModal.querySelector('#signupStaffModalLabel').textContent = languages[lang].signup_staff;
                signupModal.querySelector('label[for="signupStaffName"]').textContent = languages[lang].full_name;
                signupModal.querySelector('#signupStaffName').placeholder = languages[lang].full_name;
                signupModal.querySelector('label[for="signupStaffEmail"]').textContent = languages[lang].email;
                signupModal.querySelector('#signupStaffEmail').placeholder = languages[lang].email;
                signupModal.querySelector('label[for="signupStaffPhone"]').textContent = languages[lang].phone_number;
                signupModal.querySelector('#signupStaffPhone').placeholder = languages[lang].phone_number;
                signupModal.querySelector('label[for="signupStaffPassword"]').textContent = languages[lang].password;
                signupModal.querySelector('#signupStaffPassword').placeholder = languages[lang].password;
                signupModal.querySelector('label[for="signupStaffRole"]').textContent = languages[lang].role;
                signupModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                signupModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].save;
            }

            const changePasswordModal = document.getElementById('changePasswordModal');
            if (changePasswordModal) {
                changePasswordModal.querySelector('#changePasswordModalLabel').textContent = languages[lang].change_password;
                changePasswordModal.querySelector('label[for="emailOrPhone"]').textContent = languages[lang].email_or_phone;
                changePasswordModal.querySelector('#emailOrPhone').placeholder = languages[lang].email_or_phone;
                changePasswordModal.querySelector('label[for="newPassword"]').textContent = languages[lang].new_password;
                changePasswordModal.querySelector('#newPassword').placeholder = languages[lang].new_password;
                changePasswordModal.querySelector('label[for="confirmPassword"]').textContent = languages[lang].re_enter_new_password;
                changePasswordModal.querySelector('#confirmPassword').placeholder = languages[lang].re_enter_new_password;
                changePasswordModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                changePasswordModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].update;
            }

            const deleteModal = document.getElementById('deleteConfirmModal');
            if (deleteModal) {
                deleteModal.querySelector('#deleteConfirmModalLabel').textContent = languages[lang].confirm_delete;
                deleteModal.querySelector('.modal-body').textContent = languages[lang].confirm_delete;
                deleteModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].no;
                deleteModal.querySelector('.modal-footer .btn-danger').textContent = languages[lang].yes;
            }

            const url = new URL(window.location);
            url.searchParams.set('lang', lang);
            history.pushState({}, '', url);
        }

        function addStaff() {
            const formData = new FormData();
            formData.append('FullName', document.getElementById('staffName').value);
            formData.append('Email', document.getElementById('staffEmail').value);
            formData.append('PhoneNumber', document.getElementById('staffPhone').value);
            formData.append('Password', document.getElementById('staffPassword').value);
            formData.append('role', document.getElementById('staffRole').value);

            fetch('add_staff_manager.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages['<?php echo $selectedLang; ?>'].add_staff + ' thành công!');
                    location.reload();
                } else {
                    alert('Thêm thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi thêm nhân viên.');
            });
        }

        function signupStaff() {
            const formData = new FormData();
            formData.append('FullName', document.getElementById('signupStaffName').value);
            formData.append('Email', document.getElementById('signupStaffEmail').value);
            formData.append('PhoneNumber', document.getElementById('signupStaffPhone').value);
            formData.append('Password', document.getElementById('signupStaffPassword').value);
            formData.append('role', document.getElementById('signupStaffRole').value);

            fetch('add_staff_manager.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages['<?php echo $selectedLang; ?>'].signup_staff + ' thành công!');
                    location.reload();
                } else {
                    alert('Đăng ký thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi đăng ký nhân viên.');
            });
        }

        function loadStaffData(id, full_name, email, phone_number, password, role) {
            document.getElementById('editStaffId').value = id;
            document.getElementById('editStaffName').value = full_name;
            document.getElementById('editStaffEmail').value = email;
            document.getElementById('editStaffPhone').value = phone_number;
            document.getElementById('editStaffPassword').value = password;
            document.getElementById('editStaffRole').value = role;
        }

        function updateStaff() {
            const formData = new FormData();
            formData.append('id', document.getElementById('editStaffId').value);
            formData.append('FullName', document.getElementById('editStaffName').value);
            formData.append('Email', document.getElementById('editStaffEmail').value);
            formData.append('PhoneNumber', document.getElementById('editStaffPhone').value);
            formData.append('role', document.getElementById('editStaffRole').value);

            fetch('update_staff.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages['<?php echo $selectedLang; ?>'].edit_staff + ' thành công!');
                    location.reload();
                } else {
                    alert('Cập nhật thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi cập nhật nhân viên.');
            });
        }

        function changePassword() {
            const emailOrPhone = document.getElementById('emailOrPhone').value.trim();
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const lang = document.getElementById('language-select').value;

            if (!emailOrPhone) {
                alert(lang === 'vi' ? 'Vui lòng nhập Email hoặc Số Điện Thoại!' : 'Please enter Email or Phone Number!');
                return;
            }
            if (!newPassword) {
                alert(lang === 'vi' ? 'Vui lòng nhập Mật khẩu Mới!' : 'Please enter New Password!');
                return;
            }
            if (newPassword !== confirmPassword) {
                alert(lang === 'vi' ? 'Mật khẩu mới và xác nhận không khớp!' : 'New password and confirmation do not match!');
                return;
            }

            const formData = new FormData();
            formData.append('email_or_phone', emailOrPhone);
            formData.append('new_password', newPassword);

            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages[lang].change_password + (lang === 'vi' ? ' thành công!' : ' successful!'));
                    location.reload();
                } else {
                    alert((lang === 'vi' ? 'Thay đổi thất bại: ' : 'Change failed: ') + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert(lang === 'vi' ? 'Có lỗi xảy ra khi thay đổi mật khẩu.' : 'An error occurred while changing the password.');
            });
        }

        function setDeleteId(id) {
            deleteStaffId = id;
        }

        function deleteStaff() {
            if (deleteStaffId) {
                fetch('delete_staff.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${deleteStaffId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(languages['<?php echo $selectedLang; ?>'].confirm_delete + ' thành công!');
                        location.reload();
                    } else {
                        alert('Xóa thất bại: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra khi xóa nhân viên.');
                });
            }
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const role = document.getElementById('roleFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const lang = document.getElementById('language-select').value;
            const url = new URL(window.location);
            url.searchParams.set('search', search);
            url.searchParams.set('role', role);
            url.searchParams.set('sort', sort);
            url.searchParams.set('lang', lang);
            window.location = url;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.querySelector('tbody');
            if (tbody) {
                const rowCount = tbody.getElementsByTagName('tr').length;
                const buttonContainer = document.getElementById('button-container');
                if (rowCount > 7) {
                    buttonContainer.style.display = 'flex';
                } else {
                    buttonContainer.style.display = 'none';
                }
            }

            updateLanguage('<?php echo $selectedLang; ?>');
        });
    </script>
</body>
</html>