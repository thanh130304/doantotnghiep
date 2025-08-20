<?php
session_start(); // Start session to check login status

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle save all status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    $statusUpdates = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'status_') === 0) {
            $id = (int) str_replace('status_', '', $key);
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
    $result = $conn->query("SELECT id, full_name, phone_number, year_of_birth, facility, email, status FROM users");
}

// Filter and sort
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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

        .table-container th,
        .table-container td {
            padding: 8px;
            border: 1px solid #dee2e6;
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="text-center py-4">
                    <img src="https://daihoc.fpt.edu.vn/wp-content/uploads/2024/11/Logo-Btec.webp" alt="FPT Logo"
                        class="sidebar-logo">
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="manage.php">
                            <i class="fas fa-users me-2"></i>Manage Staff
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student_manager.php">
                            <i class="fas fa-user-graduate me-2"></i>Manage Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'logout.php' : 'login.php'; ?>">
                            <i
                                class="fas fa-sign-<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'out' : 'in'; ?>-alt me-2"></i><?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Logout' : 'Login'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupStudentModal">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Management List</h1>
                    <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
                        <a href="profile.php" class="me-3">
                            <img src="https://static.vecteezy.com/system/resources/previews/019/879/186/non_2x/user-icon-on-transparent-background-free-png.png"
                                alt="User Icon" class="img-fluid user-icon" style="max-height: 40px;" title="Profile">
                        </a>
                        <button class="btn btn-sm btn-primary" id="add-student-btn">
                            <i class="fas fa-plus me-1"></i>Add Student
                        </button>
                    </div>
                </div>

                <!-- Search and filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput"
                                        placeholder="Search students..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                    <option value="all" <?php echo empty($statusFilter) ? 'selected' : ''; ?>>All Statuses</option>
                                    <option value="not_contacted" <?php echo $statusFilter === 'not_contacted' ? 'selected' : ''; ?>>Not Contacted</option>
                                    <option value="contacted" <?php echo $statusFilter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                    <option value="interested" <?php echo $statusFilter === 'interested' ? 'selected' : ''; ?>>Interested</option>
                                    <option value="no_need" <?php echo $statusFilter === 'no_need' ? 'selected' : ''; ?>>No Need</option>
                                    <option value="unknown" <?php echo $statusFilter === 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="sortFilter" onchange="applyFilters()">
                                    <option value="" <?php echo empty($sort) ? 'selected' : ''; ?>>Sort by</option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name A-Z</option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name Z-A</option>
                                    <option value="year_asc" <?php echo $sort === 'year_asc' ? 'selected' : ''; ?>>Year of Birth Ascending</option>
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
                                                <th>Full Name</th>
                                                <th>Phone Number</th>
                                                <th>Year of Birth</th>
                                                <th>Facility</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr data-id='" . $row['id'] . "'>
                                                        <td>" . $row['id'] . "</td>
                                                        <td><span class='full-name'>" . htmlspecialchars($row['full_name']) . "</span></td>
                                                        <td><span class='phone-number'>" . htmlspecialchars($row['phone_number']) . "</span></td>
                                                        <td><span class='year-of-birth'>" . htmlspecialchars($row['year_of_birth']) . "</span></td>
                                                        <td><span class='facility'>" . htmlspecialchars($row['facility']) . "</span></td>
                                                        <td><span class='email'>" . htmlspecialchars($row['email']) . "</span></td>
                                                        <td>
                                                            <select class='form-select form-select-sm status-select' data-id='" . $row['id'] . "' onchange=\"updateStatus(this, '" . $row['id'] . "')\">
                                                                <option value='not_contacted' " . ($row['status'] === 'not_contacted' ? 'selected' : '') . ">Not Contacted</option>
                                                                <option value='contacted' " . ($row['status'] === 'contacted' ? 'selected' : '') . ">Contacted</option>
                                                                <option value='interested' " . ($row['status'] === 'interested' ? 'selected' : '') . ">Interested</option>
                                                                <option value='no_need' " . ($row['status'] === 'no_need' ? 'selected' : '') . ">No Need</option>
                                                                <option value='unknown' " . ($row['status'] === 'unknown' ? 'selected' : '') . ">Unknown</option>
                                                            </select>
                                                            <input type='hidden' name='status_" . $row['id'] . "' value='" . $row['status'] . "'>
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
                                                echo "<tr><td colspan='8' class='text-center text-muted'>No records found</td></tr>";
                                            }
                                            $stmt->close();
                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="button-container d-flex mt-3" id="button-container">
                                    <div>
                                        <button type="button" id="export-excel"
                                            class="btn btn-excel">Export to Excel</button>
                                        <button type="submit" name="save_all"
                                            class="btn btn-save-all ms-2">Save All</button>
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
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="studentName" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phoneNumber" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="yearOfBirth" class="form-label">Year of Birth</label>
                            <input type="number" class="form-control" id="yearOfBirth" placeholder="Year of Birth" required>
                        </div>
                        <div class="mb-3">
                            <label for="facility" class="form-label">Facility</label>
                            <select class="form-select" id="facility" required>
                                <option value="" disabled selected>Select Facility</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP.Hồ Chí Minh">TP.Hồ Chí Minh</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addStudent()">Save</button

                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="editStudentId">
                        <div class="mb-3">
                            <label for="editStudentName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editStudentName" placeholder="Full Name">
                        </div>
                        <div class="mb-3">
                            <label for="editPhoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editPhoneNumber" placeholder="Phone Number">
                        </div>
                        <div class="mb-3">
                            <label for="editYearOfBirth" class="form-label">Year of Birth</label>
                            <input type="number" class="form-control" id="editYearOfBirth" placeholder="Year of Birth">
                        </div>
                        <div class="mb-3">
                            <label for="editFacility" class="form-label">Facility</label>
                            <input type="text" class="form-control" id="editFacility" placeholder="Facility">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" placeholder="Email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateStudent()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Up Student Modal -->
    <div class="modal fade" id="signupStudentModal" tabindex="-1" aria-labelledby="signupStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupStudentModalLabel">Sign Up Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupStudentForm">
                        <div class="mb-3">
                            <label for="signupStudentName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="signupStudentName" placeholder="Full Name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupPhoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="signupPhoneNumber" placeholder="Phone Number"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupYearOfBirth" class="form-label">Year of Birth</label>
                            <input type="number" class="form-control" id="signupYearOfBirth" placeholder="Year of Birth"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="signupFacility" class="form-label">Facility</label>
                            <select class="form-select" id="signupFacility" required>
                                <option value="" disabled selected>Select Facility</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP.Hồ Chí Minh">TP.Hồ Chí Minh</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="signupEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="signupEmail" placeholder="Email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="signupStudent()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Are you sure you want to delete this student?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this student?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" onclick="deleteStudent()">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteStudentId = null;
        const isLoggedIn = <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false'; ?>;

        function updateStatus(select, id) {
            const newStatus = select.value;
            const hiddenInput = document.querySelector(`input[name='status_${id}']`);

            hiddenInput.value = newStatus;

            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&status=${newStatus}`
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Update failed:', data.error);
                        alert('Update failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Please login before accessing');
                });
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
                        alert('Add Student successful!');
                        location.reload();
                    } else {
                        alert('Add Student failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Please login before accessing');
                });
        }

        function signupStudent() {
            const formData = new FormData();
            formData.append('full_name', document.getElementById('signupStudentName').value);
            formData.append('phone_number', document.getElementById('signupPhoneNumber').value);
            formData.append('year_of_birth', document.getElementById('signupYearOfBirth').value);
            formData.append('facility', document.getElementById('signupFacility').value);
            formData.append('email', document.getElementById('signupEmail').value);
            formData.append('status', 'unknown');

            fetch('add_student.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sign Up Student successful!');
                        location.reload();
                    } else {
                        alert('Sign Up Student failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Please login before accessing');
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
                        alert('Edit Student successful!');
                        location.reload();
                    } else {
                        alert('Edit Student failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Please login before accessing');
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
                            alert('Delete successful!');
                            location.reload();
                        } else {
                            alert('Delete failed: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Please login before accessing');
                    });
            }
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const url = new URL(window.location);
            url.searchParams.set('search', search);
            url.searchParams.set('status', status);
            url.searchParams.set('sort', sort);
            window.location = url;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const addStudentBtn = document.getElementById('add-student-btn');

            // Handle click on Add Student button
            if (addStudentBtn) {
                addStudentBtn.addEventListener('click', function (event) {
                    event.preventDefault(); // Prevent default behavior
                    if (isLoggedIn) {
                        // Manually open modal if logged in
                        const addStudentModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
                        addStudentModal.show();
                    } else {
                        // Show alert if not logged in
                        if (confirm('Please login before accessing')) {
                            window.location.href = 'login.php';
                        }
                    }
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

            document.getElementById('export-excel').addEventListener('click', function () {
                const table = document.querySelector('table');
                const rows = Array.from(table.querySelectorAll('tr'));
                const data = [];

                const headers = Array.from(rows[0].querySelectorAll('th'))
                    .slice(0, -1)
                    .map(th => th.textContent);

                rows.slice(1).forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td')).slice(0, -1);
                    if (cells.length > 0) {
                        const rowData = {};
                        headers.forEach((header, index) => {
                            let cellContent = cells[index].textContent.trim();
                            if (index === headers.length - 1) {
                                const statusSelect = cells[index].querySelector('.status-select');
                                if (statusSelect) {
                                    cellContent = statusSelect.options[statusSelect.selectedIndex].text;
                                }
                            }
                            rowData[header] = cellContent;
                        });
                        data.push(rowData);
                    }
                });

                const ws = XLSX.utils.json_to_sheet(data, { header: headers });
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
                const blob = new Blob([wbout], { type: 'application/octet-stream' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', 'Student_List.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</body>

</html>