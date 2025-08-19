<?php
session_start(); // Start session to check login status

$conn = new mysqli('localhost', 'root', '', 'btec_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ID, FullName, Email, PhoneNumber, Password, role FROM staff";
$result = $conn->query($sql);

// Filter and sort
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management List</title>
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
                    <img src="https://daihoc.fpt.edu.vn/wp-content/uploads/2024/11/Logo-Btec.webp" alt="FPT Logo"
                        class="sidebar-logo">
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="manage.php">
                            <i class="fas fa-users me-2"></i>Manage Staff
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student_manager.php">
                            <i class="fas fa-user-graduate me-2"></i>Manage Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'logout.php' : 'login.php'; ?>">
                            <i class="fas fa-sign-<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'out' : 'in'; ?>-alt me-2"></i><?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Logout' : 'Login'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupStaffModal">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Staff Management List</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="fas fa-plus me-1"></i>Add Staff
                        </button>
                    </div>
                </div>

                <!-- Search and filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search staff..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="roleFilter" onchange="applyFilters()">
                                    <option value="all" <?php echo empty($roleFilter) ? 'selected' : ''; ?>>All roles</option>
                                    <option value="manager" <?php echo $roleFilter === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                    <option value="staff" <?php echo $roleFilter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="sortFilter" onchange="applyFilters()">
                                    <option value="" <?php echo empty($sort) ? 'selected' : ''; ?>>Sort by</option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name A-Z</option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name Z-A</option>
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
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Password</th>
                                            <th>Role</th>
                                            <th>Actions</th>
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
                                            echo "<tr><td colspan='7' class='text-center text-muted'>No records found</td></tr>";
                                        }
                                        $stmt->close();
                                        $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="button-container d-flex mt-3" id="button-container">
                                <button type="button" id="change-password-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
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
                    <h5 class="modal-title" id="addStaffModalLabel">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <div class="mb-3">
                            <label for="staffName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="staffName" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="staffEmail" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="staffPhone" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="staffPassword" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffRole" class="form-label">Role</label>
                            <select class="form-select" id="staffRole" required>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addStaff()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm">
                        <input type="hidden" id="editStaffId">
                        <div class="mb-3">
                            <label for="editStaffName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editStaffName" placeholder="Full Name">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editStaffEmail" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editStaffPhone" placeholder="Phone Number">
                        </div>
                        <div class="mb-3">
                            <label for="editStaffPassword" class="form-label">Password</label>
                            <input type="text" class="form-control" id="editStaffPassword" placeholder="Password" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editStaffRole" class="form-label">Role</label>
                            <select class="form-select" id="editStaffRole">
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateStaff()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Up Staff Modal -->
    <div class="modal fade" id="signupStaffModal" tabindex="-1" aria-labelledby="signupStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupStaffModalLabel">Sign Up Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupStaffForm">
                        <div class="mb-3">
                            <label for="signupStaffName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="signupStaffName" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="signupStaffEmail" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="signupStaffPhone" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="signupStaffPassword" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupStaffRole" class="form-label">Role</label>
                            <select class="form-select" id="signupStaffRole" required>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="signupStaff()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="emailOrPhone" class="form-label">Email or Phone Number</label>
                            <input type="text" class="form-control" id="emailOrPhone" placeholder="Email or Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" placeholder="New Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Re-enter New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Re-enter New Password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="changePassword()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Are you sure you want to delete this staff?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this staff?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" onclick="deleteStaff()">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteStaffId = null;
        const isLoggedIn = <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false'; ?>;

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
                    alert('Add Staff successful!');
                    location.reload();
                } else {
                    alert('Add failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding staff.');
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
                    alert('Sign Up Staff successful!');
                    location.reload();
                } else {
                    alert('Sign Up failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while signing up staff.');
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
                    alert('Edit Staff successful!');
                    location.reload();
                } else {
                    alert('Update failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating staff.');
            });
        }

        function changePassword() {
            const emailOrPhone = document.getElementById('emailOrPhone').value.trim();
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!emailOrPhone) {
                alert('Please enter Email or Phone Number!');
                return;
            }
            if (!newPassword) {
                alert('Please enter New Password!');
                return;
            }
            if (newPassword !== confirmPassword) {
                alert('New password and confirmation do not match!');
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
                    alert('Change Password successful!');
                    location.reload();
                } else {
                    alert('Change failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while changing the password.');
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
                        alert('Delete Staff successful!');
                        location.reload();
                    } else {
                        alert('Delete failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting staff.');
                });
            }
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const role = document.getElementById('roleFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const url = new URL(window.location);
            url.searchParams.set('search', search);
            url.searchParams.set('role', role);
            url.searchParams.set('sort', sort);
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
        });
    </script>
</body>
</html>