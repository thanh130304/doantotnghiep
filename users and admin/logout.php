<?php
session_start();

// Xóa tất cả dữ liệu session
session_unset();
session_destroy();

// Chuyển hướng về trang users.php
header("Location: users.php");
exit();
?>