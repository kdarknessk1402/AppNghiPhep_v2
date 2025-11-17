<?php
// views/auth/logout.php - Đăng xuất

require_once __DIR__ . '/../../includes/session.php';

// Log hoạt động
if (isLoggedIn()) {
    logActivity('LOGOUT', 'Đăng xuất khỏi hệ thống');
}

// Đăng xuất
logout();
?>