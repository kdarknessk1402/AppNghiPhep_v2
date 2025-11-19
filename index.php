<?php
require_once __DIR__ . '/includes/init.php';

// Nếu đã đăng nhập, redirect theo vai trò
if (isLoggedIn()) {
    redirectByRole($_SESSION['vai_tro']);
}

// Nếu chưa đăng nhập, redirect đến trang login
header('Location: /appnghiphep_v2/views/auth/login.php');
exit;
?>