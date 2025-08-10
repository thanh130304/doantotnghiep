<?php
$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, full_name, phone_number, year_of_birth, facility, email, status FROM users";
$result = $conn->query($sql);

// Xử lý lưu tất cả khi nhấn Save All
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    $statusUpdates = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'status_') === 0) {
            $id = (int)str_replace('status_', '', $key);
            $status = in_array($value, ['not_contacted', 'contacted', 'interested', 'no_need', 'unknown']) ? $value : 'unknown';
            $statusUpdates[$id] = $status;
        }
    }

    foreach ($statusUpdates as $id => $status) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }
    $result = $conn->query($sql);
}

// Lọc và sắp xếp
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$whereClauses = [];
$params = [];
if (!empty($search)) {
    $whereClauses[] = "(full_name LIKE ? OR phone_number LIKE ? OR facility LIKE ? OR email LIKE ?)";
    $searchParam = "%$search%";
    $params = array_fill(0, 4, $searchParam);
}
if (!empty($statusFilter) && $statusFilter !== 'all') {
    $whereClauses[] = "status = ?";
    $params[] = $statusFilter;
}

$sql = "SELECT id, full_name, phone_number, year_of_birth, facility, email, status FROM users";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$orderBy = '';
switch ($sort) {
    case 'name_asc':
        $orderBy = "ORDER BY full_name ASC";
        break;
    case 'name_desc':
        $orderBy = "ORDER BY full_name DESC";
        break;
    case 'year_asc':
        $orderBy = "ORDER BY year_of_birth ASC";
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

$selectedLang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

$languages = [
    'en' => [
        'title' => 'Student Management List',
        'full_name' => 'Full Name',
        'phone_number' => 'Phone Number',
        'year_of_birth' => 'Year of Birth',
        'facility' => 'Facility',
        'email' => 'Email',
        'no_records' => 'No records found',
        'export_excel' => 'Export to Excel',
        'status' => 'Status',
        'unknown' => 'Unknown',
        'not_contacted' => 'Not contacted',
        'contacted' => 'Contacted',
        'interested' => 'Interested',
        'no_need' => 'No need',
        'save_all' => 'Save All',
        'add_student' => 'Add Student',
        'edit_student' => 'Edit Student',
        'confirm_delete' => 'Are you sure you want to delete this student?',
        'yes' => 'Yes',
        'no' => 'No',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'update' => 'Update',
        'search_placeholder' => 'Search students...',
        'all_statuses' => 'All statuses',
        'sort_by' => 'Sort by',
        'name_asc' => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'year_asc' => 'Year of Birth Ascending',
        'select_facility' => 'Select facility',
        'manage_students' => 'Manage Students',
        'logout' => 'Logout',
        'actions' => 'Actions'
    ],
    'vi' => [
        'title' => 'Danh Sách Học Viên Đăng Ký Tư Vấn Zalo',
        'full_name' => 'Họ và Tên',
        'phone_number' => 'Số Điện Thoại',
        'year_of_birth' => 'Năm Sinh',
        'facility' => 'Cơ Sở',
        'email' => 'Email',
        'no_records' => 'Không tìm thấy bản ghi nào',
        'export_excel' => 'Xuất sang Excel',
        'status' => 'Trạng Thái',
        'unknown' => 'Chưa Xác Định',
        'not_contacted' => 'Chưa Liên Hệ',
        'contacted' => 'Đã Liên Hệ',
        'interested' => 'Quan Tâm',
        'no_need' => 'Không Cần',
        'save_all' => 'Lưu Tất Cả',
        'add_student' => 'Thêm Học Viên',
        'edit_student' => 'Chỉnh Sửa Học Viên',
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa học viên này?',
        'yes' => 'Có',
        'no' => 'Không',
        'cancel' => 'Hủy',
        'save' => 'Lưu',
        'update' => 'Cập nhật',
        'search_placeholder' => 'Tìm kiếm học viên...',
        'all_statuses' => 'Tất cả trạng thái',
        'sort_by' => 'Sắp xếp theo',
        'name_asc' => 'Họ tên A-Z',
        'name_desc' => 'Họ tên Z-A',
        'year_asc' => 'Năm sinh tăng dần',
        'select_facility' => 'Chọn cơ sở',
        'manage_students' => 'Quản lý Học viên',
        'logout' => 'Đăng xuất',
        'actions' => 'Hành động'
    ]
];

function mapStatus($status) {
    $statusMap = [
        'not_contacted' => 'not_contacted',
        'contacted' => 'contacted',
        'interested' => 'interested',
        'no_need' => 'no_need',
        'unknown' => 'unknown'
    ];
    $status = strtolower(str_replace(' ', '_', $status));
    return $statusMap[$status] ?? 'unknown';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $selectedLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $languages[$selectedLang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40; /* Reverted to original dark gray */
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
        .user-icon {
            transition: filter 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }
        .user-icon:hover {
            filter: hue-rotate(90deg) saturate(2);
            transform: scale(1.1);
        }
        .user-icon:active {
            filter: hue-rotate(90deg) saturate(2) brightness(0.8);
            transform: scale(1);
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
        .dropdown .status-btn {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            width: 100%;
            text-align: left;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='currentColor' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 160px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1;
            margin-top: 2px;
        }
        .dropdown-content button {
            color: #000;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
        }
        .dropdown-content button:hover {
            background-color: #f0f0f0;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .btn-excel {
            background-color: #008000;
            color: #ffffff;
            border: none;
        }
        .btn-save-all {
            background-color: #ff0000;
            color: #ffffff;
            border: none;
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
                        <a class="nav-link active" href="#" data-lang-key="manage_students">
                            <i class="fas fa-users me-2"></i><?php echo $languages[$selectedLang]['manage_students']; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php" data-lang-key="logout">
                            <i class="fas fa-sign-out-alt me-2"></i><?php echo $languages[$selectedLang]['logout']; ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $languages[$selectedLang]['title']; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <img src="https://static.vecteezy.com/system/resources/previews/019/879/186/non_2x/user-icon-on-transparent-background-free-png.png"
                             alt="User Icon" class="img-fluid me-3 user-icon" style="max-height: 40px;" title="Profile">
                        <select id="language-select" class="form-select me-2" style="width: auto;" onchange="updateLanguage(this.value)">
                            <option value="en" <?php echo $selectedLang === 'en' ? 'selected' : ''; ?>>English</option>
                            <option value="vi" <?php echo $selectedLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
                        </select>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fas fa-plus me-1"></i> <?php echo $languages[$selectedLang]['add_student']; ?>
                        </button>
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
                                <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                    <option value="all" <?php echo empty($statusFilter) ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['all_statuses']; ?></option>
                                    <option value="not_contacted" <?php echo $statusFilter === 'not_contacted' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['not_contacted']; ?></option>
                                    <option value="contacted" <?php echo $statusFilter === 'contacted' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['contacted']; ?></option>
                                    <option value="interested" <?php echo $statusFilter === 'interested' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['interested']; ?></option>
                                    <option value="no_need" <?php echo $statusFilter === 'no_need' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['no_need']; ?></option>
                                    <option value="unknown" <?php echo $statusFilter === 'unknown' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['unknown']; ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="sortFilter" onchange="applyFilters()">
                                    <option value="" <?php echo empty($sort) ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['sort_by']; ?></option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['name_asc']; ?></option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['name_desc']; ?></option>
                                    <option value="year_asc" <?php echo $sort === 'year_asc' ? 'selected' : ''; ?>><?php echo $languages[$selectedLang]['year_asc']; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student table -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="status-form">
                            <div class="table-responsive">
                                <div class="table-container">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th data-lang-key="full_name"><?php echo $languages[$selectedLang]['full_name']; ?></th>
                                                <th data-lang-key="phone_number"><?php echo $languages[$selectedLang]['phone_number']; ?></th>
                                                <th data-lang-key="year_of_birth"><?php echo $languages[$selectedLang]['year_of_birth']; ?></th>
                                                <th data-lang-key="facility"><?php echo $languages[$selectedLang]['facility']; ?></th>
                                                <th data-lang-key="email"><?php echo $languages[$selectedLang]['email']; ?></th>
                                                <th data-lang-key="status"><?php echo $languages[$selectedLang]['status']; ?></th>
                                                <th data-lang-key="actions"><?php echo $languages[$selectedLang]['actions']; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $mappedStatus = mapStatus($row['status']);
                                                    $displayStatus = $languages[$selectedLang][$mappedStatus] ?? $languages[$selectedLang]['unknown'];
                                                    echo "<tr data-id='" . $row['id'] . "'>
                                                        <td>" . $row['id'] . "</td>
                                                        <td><span class='full-name'>" . htmlspecialchars($row['full_name']) . "</span></td>
                                                        <td><span class='phone-number'>" . htmlspecialchars($row['phone_number']) . "</span></td>
                                                        <td><span class='year-of-birth'>" . htmlspecialchars($row['year_of_birth']) . "</span></td>
                                                        <td><span class='facility'>" . htmlspecialchars($row['facility']) . "</span></td>
                                                        <td><span class='email'>" . htmlspecialchars($row['email']) . "</span></td>
                                                        <td>
                                                            <div class='dropdown'>
                                                                <button class='status-btn' data-id='" . $row['id'] . "' data-status='" . htmlspecialchars($mappedStatus) . "'>" . $displayStatus . "</button>
                                                                <div class='dropdown-content'>
                                                                    <button type='button' class='status-option' data-value='not_contacted' onclick=\"updateStatus(this, '" . $row['id'] . "', '" . $selectedLang . "')\">" . $languages[$selectedLang]['not_contacted'] . "</button>
                                                                    <button type='button' class='status-option' data-value='contacted' onclick=\"updateStatus(this, '" . $row['id'] . "', '" . $selectedLang . "')\">" . $languages[$selectedLang]['contacted'] . "</button>
                                                                    <button type='button' class='status-option' data-value='interested' onclick=\"updateStatus(this, '" . $row['id'] . "', '" . $selectedLang . "')\">" . $languages[$selectedLang]['interested'] . "</button>
                                                                    <button type='button' class='status-option' data-value='no_need' onclick=\"updateStatus(this, '" . $row['id'] . "', '" . $selectedLang . "')\">" . $languages[$selectedLang]['no_need'] . "</button>
                                                                </div>
                                                            </div>
                                                            <input type='hidden' name='status_" . $row['id'] . "' value='" . $mappedStatus . "'>
                                                        </td>
                                                        <td>
                                                            <button type='button' class='btn btn-sm btn-edit' data-bs-toggle='modal' data-bs-target='#editStudentModal' onclick=\"loadStudentData('" . $row['id'] . "', '" . htmlspecialchars($row['full_name']) . "', '" . htmlspecialchars($row['phone_number']) . "', '" . htmlspecialchars($row['year_of_birth']) . "', '" . htmlspecialchars($row['facility']) . "', '" . htmlspecialchars($row['email']) . "')\">
                                                                <i class='fas fa-edit'></i>
                                                            </button>
                                                            <button type='button' class='btn btn-sm btn-delete' data-bs-toggle='modal' data-bs-target='#deleteConfirmModal' onclick=\"setDeleteId('" . $row['id'] . "')\">
                                                                <i class='fas fa-trash'></i>
                                                            </button>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center text-muted'>" . $languages[$selectedLang]['no_records'] . "</td></tr>";
                                            }
                                            $stmt->close();
                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="button-container d-flex mt-3" id="button-container">
                                    <div>
                                        <button type="button" id="export-excel" class="btn btn-excel"><?php echo $languages[$selectedLang]['export_excel']; ?></button>
                                        <button type="submit" name="save_all" class="btn btn-save-all ms-2"><?php echo $languages[$selectedLang]['save_all']; ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel"><?php echo $languages[$selectedLang]['add_student']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label for="studentName" class="form-label"><?php echo $languages[$selectedLang]['full_name']; ?></label>
                            <input type="text" class="form-control" id="studentName" placeholder="<?php echo $languages[$selectedLang]['full_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label"><?php echo $languages[$selectedLang]['phone_number']; ?></label>
                            <input type="tel" class="form-control" id="phoneNumber" placeholder="<?php echo $languages[$selectedLang]['phone_number']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="yearOfBirth" class="form-label"><?php echo $languages[$selectedLang]['year_of_birth']; ?></label>
                            <input type="number" class="form-control" id="yearOfBirth" placeholder="<?php echo $languages[$selectedLang]['year_of_birth']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="facility" class="form-label"><?php echo $languages[$selectedLang]['facility']; ?></label>
                            <select class="form-select" id="facility" required>
                                <option value="" disabled selected><?php echo $languages[$selectedLang]['select_facility']; ?></option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP.Hồ Chí Minh">TP.Hồ Chí Minh</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo $languages[$selectedLang]['email']; ?></label>
                            <input type="email" class="form-control" id="email" placeholder="<?php echo $languages[$selectedLang]['email']; ?>" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="addStudent()"><?php echo $languages[$selectedLang]['save']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel"><?php echo $languages[$selectedLang]['edit_student']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="editStudentId">
                        <div class="mb-3">
                            <label for="editStudentName" class="form-label"><?php echo $languages[$selectedLang]['full_name']; ?></label>
                            <input type="text" class="form-control" id="editStudentName" placeholder="<?php echo $languages[$selectedLang]['full_name']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editPhoneNumber" class="form-label"><?php echo $languages[$selectedLang]['phone_number']; ?></label>
                            <input type="tel" class="form-control" id="editPhoneNumber" placeholder="<?php echo $languages[$selectedLang]['phone_number']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editYearOfBirth" class="form-label"><?php echo $languages[$selectedLang]['year_of_birth']; ?></label>
                            <input type="number" class="form-control" id="editYearOfBirth" placeholder="<?php echo $languages[$selectedLang]['year_of_birth']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editFacility" class="form-label"><?php echo $languages[$selectedLang]['facility']; ?></label>
                            <input type="text" class="form-control" id="editFacility" placeholder="<?php echo $languages[$selectedLang]['facility']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label"><?php echo $languages[$selectedLang]['email']; ?></label>
                            <input type="email" class="form-control" id="editEmail" placeholder="<?php echo $languages[$selectedLang]['email']; ?>">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $languages[$selectedLang]['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="updateStudent()"><?php echo $languages[$selectedLang]['update']; ?></button>
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
                    <button type="button" class="btn btn-danger" onclick="deleteStudent()"><?php echo $languages[$selectedLang]['yes']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const languages = <?php echo json_encode($languages); ?>;
        let deleteStudentId = null;

        function updateLanguage(lang) {
            // Update main content
            document.querySelector('h1.h2').textContent = languages[lang].title;
            document.querySelector('th[data-lang-key="full_name"]').textContent = languages[lang].full_name;
            document.querySelector('th[data-lang-key="phone_number"]').textContent = languages[lang].phone_number;
            document.querySelector('th[data-lang-key="year_of_birth"]').textContent = languages[lang].year_of_birth;
            document.querySelector('th[data-lang-key="facility"]').textContent = languages[lang].facility;
            document.querySelector('th[data-lang-key="email"]').textContent = languages[lang].email;
            document.querySelector('th[data-lang-key="status"]').textContent = languages[lang].status;
            document.querySelector('th[data-lang-key="actions"]').textContent = languages[lang].actions;
            document.getElementById('export-excel').textContent = languages[lang].export_excel;
            document.querySelector('button[name="save_all"]').textContent = languages[lang].save_all;
            document.querySelector('#searchInput').placeholder = languages[lang].search_placeholder;
            document.querySelector('#statusFilter option[value="all"]').textContent = languages[lang].all_statuses;
            document.querySelector('#statusFilter option[value="not_contacted"]').textContent = languages[lang].not_contacted;
            document.querySelector('#statusFilter option[value="contacted"]').textContent = languages[lang].contacted;
            document.querySelector('#statusFilter option[value="interested"]').textContent = languages[lang].interested;
            document.querySelector('#statusFilter option[value="no_need"]').textContent = languages[lang].no_need;
            document.querySelector('#statusFilter option[value="unknown"]').textContent = languages[lang].unknown;
            document.querySelector('#sortFilter option[value=""]').textContent = languages[lang].sort_by;
            document.querySelector('#sortFilter option[value="name_asc"]').textContent = languages[lang].name_asc;
            document.querySelector('#sortFilter option[value="name_desc"]').textContent = languages[lang].name_desc;
            document.querySelector('#sortFilter option[value="year_asc"]').textContent = languages[lang].year_asc;
            document.querySelector('button[data-bs-target="#addStudentModal"]').innerHTML = `<i class="fas fa-plus me-1"></i> ${languages[lang].add_student}`;

            // Update sidebar navigation
            document.querySelector('a[data-lang-key="manage_students"]').innerHTML = `<i class="fas fa-users me-2"></i>${languages[lang].manage_students}`;
            document.querySelector('a[data-lang-key="logout"]').innerHTML = `<i class="fas fa-sign-out-alt me-2"></i>${languages[lang].logout}`;

            // Update status buttons in table
            document.querySelectorAll('.status-btn').forEach(function(btn) {
                const status = btn.getAttribute('data-status');
                btn.textContent = languages[lang][status] || languages[lang].unknown;
                const dropdown = btn.nextElementSibling;
                dropdown.innerHTML = `
                    <button type='button' class='status-option' data-value='not_contacted' onclick="updateStatus(this, '${btn.getAttribute('data-id')}', '${lang}')">${languages[lang].not_contacted}</button>
                    <button type='button' class='status-option' data-value='contacted' onclick="updateStatus(this, '${btn.getAttribute('data-id')}', '${lang}')">${languages[lang].contacted}</button>
                    <button type='button' class='status-option' data-value='interested' onclick="updateStatus(this, '${btn.getAttribute('data-id')}', '${lang}')">${languages[lang].interested}</button>
                    <button type='button' class='status-option' data-value='no_need' onclick="updateStatus(this, '${btn.getAttribute('data-id')}', '${lang}')">${languages[lang].no_need}</button>
                `;
            });

            // Update Add Student Modal
            const addModal = document.getElementById('addStudentModal');
            if (addModal) {
                addModal.querySelector('#addStudentModalLabel').textContent = languages[lang].add_student;
                addModal.querySelector('label[for="studentName"]').textContent = languages[lang].full_name;
                addModal.querySelector('#studentName').placeholder = languages[lang].full_name;
                addModal.querySelector('label[for="phoneNumber"]').textContent = languages[lang].phone_number;
                addModal.querySelector('#phoneNumber').placeholder = languages[lang].phone_number;
                addModal.querySelector('label[for="yearOfBirth"]').textContent = languages[lang].year_of_birth;
                addModal.querySelector('#yearOfBirth').placeholder = languages[lang].year_of_birth;
                addModal.querySelector('label[for="facility"]').textContent = languages[lang].facility;
                addModal.querySelector('#facility option[value=""]').textContent = languages[lang].select_facility;
                addModal.querySelector('label[for="email"]').textContent = languages[lang].email;
                addModal.querySelector('#email').placeholder = languages[lang].email;
                addModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                addModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].save;
            }

            // Update Edit Student Modal
            const editModal = document.getElementById('editStudentModal');
            if (editModal) {
                editModal.querySelector('#editStudentModalLabel').textContent = languages[lang].edit_student;
                editModal.querySelector('label[for="editStudentName"]').textContent = languages[lang].full_name;
                editModal.querySelector('#editStudentName').placeholder = languages[lang].full_name;
                editModal.querySelector('label[for="editPhoneNumber"]').textContent = languages[lang].phone_number;
                editModal.querySelector('#editPhoneNumber').placeholder = languages[lang].phone_number;
                editModal.querySelector('label[for="editYearOfBirth"]').textContent = languages[lang].year_of_birth;
                editModal.querySelector('#editYearOfBirth').placeholder = languages[lang].year_of_birth;
                editModal.querySelector('label[for="editFacility"]').textContent = languages[lang].facility;
                editModal.querySelector('#editFacility').placeholder = languages[lang].facility;
                editModal.querySelector('label[for="editEmail"]').textContent = languages[lang].email;
                editModal.querySelector('#editEmail').placeholder = languages[lang].email;
                editModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].cancel;
                editModal.querySelector('.modal-footer .btn-primary').textContent = languages[lang].update;
            }

            // Update Delete Confirmation Modal
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (deleteModal) {
                deleteModal.querySelector('#deleteConfirmModalLabel').textContent = languages[lang].confirm_delete;
                deleteModal.querySelector('.modal-body').textContent = languages[lang].confirm_delete;
                deleteModal.querySelector('.modal-footer .btn-secondary').textContent = languages[lang].no;
                deleteModal.querySelector('.modal-footer .btn-danger').textContent = languages[lang].yes;
            }

            // Update URL with selected language
            history.pushState({}, '', `?lang=${lang}`);
        }

        function updateStatus(option, id, lang) {
            const btn = option.parentElement.previousElementSibling;
            const newStatus = option.getAttribute('data-value');
            const hiddenInput = document.querySelector(`input[name='status_${id}']`);

            btn.setAttribute('data-status', newStatus);
            btn.textContent = languages[lang][newStatus] || languages[lang].unknown;
            hiddenInput.value = newStatus;

            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.setAttribute('data-status', data.status);
                    btn.textContent = languages[lang][data.status] || languages[lang].unknown;
                    hiddenInput.value = data.status;
                } else {
                    console.error('Update failed:', data.error);
                    alert('Failed to update status: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating status.');
            });

            option.parentElement.style.display = 'none';
        }

        function addStudent() {
            const formData = new FormData();
            formData.append('full_name', document.getElementById('studentName').value);
            formData.append('phone_number', document.getElementById('phoneNumber').value);
            formData.append('year_of_birth', document.getElementById('yearOfBirth').value);
            formData.append('facility', document.getElementById('facility').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('status', 'unknown');

            fetch('add_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages['<?php echo $selectedLang; ?>'].add_student + ' thành công!');
                    location.reload();
                } else {
                    alert('Thêm thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm học viên.');
            });
        }

        function loadStudentData(id, full_name, phone_number, year_of_birth, facility, email) {
            document.getElementById('editStudentId').value = id;
            document.getElementById('editStudentName').value = full_name;
            document.getElementById('editPhoneNumber').value = phone_number;
            document.getElementById('editYearOfBirth').value = year_of_birth;
            document.getElementById('editFacility').value = facility;
            document.getElementById('editEmail').value = email;
        }

        function updateStudent() {
            const formData = new FormData();
            formData.append('id', document.getElementById('editStudentId').value);
            formData.append('full_name', document.getElementById('editStudentName').value);
            formData.append('phone_number', document.getElementById('editPhoneNumber').value);
            formData.append('year_of_birth', document.getElementById('editYearOfBirth').value);
            formData.append('facility', document.getElementById('editFacility').value);
            formData.append('email', document.getElementById('editEmail').value);

            fetch('update_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(languages['<?php echo $selectedLang; ?>'].edit_student + ' thành công!');
                    location.reload();
                } else {
                    alert('Cập nhật thất bại: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật học viên.');
            });
        }

        function setDeleteId(id) {
            deleteStudentId = id;
        }

        function deleteStudent() {
            if (deleteStudentId) {
                fetch('delete_student.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${deleteStudentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa học viên thành công!');
                        location.reload();
                    } else {
                        alert('Xóa thất bại: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa học viên.');
                });
            }
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const lang = document.getElementById('language-select').value;
            const url = new URL(window.location);
            url.searchParams.set('search', search);
            url.searchParams.set('status', status);
            url.searchParams.set('sort', sort);
            url.searchParams.set('lang', lang);
            window.location = url;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const userIcon = document.querySelector('.user-icon');
            if (userIcon) {
                userIcon.addEventListener('click', function () {
                    window.location.href = 'profile.php';
                });
            }

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

            document.getElementById('export-excel').addEventListener('click', function() {
                const table = document.querySelector('table');
                const rows = Array.from(table.querySelectorAll('tr'));
                const data = [];
                const lang = document.getElementById('language-select').value;

                // Extract headers (excluding the last column: Actions)
                const headers = Array.from(rows[0].querySelectorAll('th'))
                    .slice(0, -1)
                    .map(th => th.textContent);

                // Extract row data (excluding the last column: Actions)
                rows.slice(1).forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td')).slice(0, -1);
                    if (cells.length > 0) { // Skip empty rows (e.g., "No records found")
                        const rowData = {};
                        headers.forEach((header, index) => {
                            let cellContent = cells[index].textContent.trim();
                            // Translate status back to internal value for consistency
                            if (index === headers.length - 1) { // Status column
                                const statusBtn = cells[index].querySelector('.status-btn');
                                if (statusBtn) {
                                    const statusValue = statusBtn.getAttribute('data-status');
                                    cellContent = languages[lang][statusValue] || languages[lang].unknown;
                                }
                            }
                            rowData[header] = cellContent;
                        });
                        data.push(rowData);
                    }
                });

                // Create worksheet
                const ws = XLSX.utils.json_to_sheet(data, { header: headers });
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
                const blob = new Blob([wbout], { type: 'application/octet-stream' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', 'Danh_sach_sinh_vien_dang_ky.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            document.querySelectorAll('.status-btn').forEach(function(btn) {
                btn.addEventListener('click', function(event) {
                    event.preventDefault();
                    const dropdown = this.nextElementSibling;
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });
            });

            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.dropdown-content');
                dropdowns.forEach(function(dropdown) {
                    if (dropdown && !dropdown.contains(event.target) && !event.target.classList.contains('status-btn')) {
                        dropdown.style.display = 'none';
                    }
                });
            });

            updateLanguage('<?php echo $selectedLang; ?>');
        });
    </script>
</body>
</html>