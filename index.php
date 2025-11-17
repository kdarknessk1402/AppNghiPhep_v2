<?php
// index.php - Trang chủ (Redirect đến dashboard theo vai trò)

require_once __DIR__ . '/includes/session.php';

// Nếu chưa đăng nhập, chuyển đến trang login
if (!isLoggedIn()) {
    header('Location: /appnghiphep_v2/views/auth/login.php');
    exit;
}

// Nếu đã đăng nhập, chuyển đến dashboard theo vai trò
redirectByRole();
?>