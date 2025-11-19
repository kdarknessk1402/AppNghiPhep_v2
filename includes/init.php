<?php
// includes/init.php - File khởi tạo hệ thống, include ĐẦU TIÊN trong mọi trang

// Tránh include nhiều lần
if (defined('SYSTEM_INIT')) {
    return;
}
define('SYSTEM_INIT', true);

// 1. Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Include database connection + helper
require_once __DIR__ . '/../config/database.php';

// Chia sẻ kết nối PDO toàn cục để các hàm trong session.php sử dụng
if (!isset($GLOBALS['pdo'])) {
    $GLOBALS['pdo'] = getDBConnection();
}

// Biến cục bộ tiện dùng trong các file require init.php
$pdo = $GLOBALS['pdo'];

// 3. Include các functions cần thiết
require_once __DIR__ . '/functions.php';

require_once __DIR__ . '/session.php';


// 4. Kiểm tra đăng nhập (chỉ với trang cần auth)
// Các trang public (login, register) sẽ KHÔNG gọi hàm này
function requireAuth($allowedRoles = []) {
    // Kiểm tra đã đăng nhập chưa
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['vai_tro'])) {
        header('Location: /appnghiphep_v2/views/auth/login.php');
        exit;
    }
    
    // Kiểm tra quyền truy cập theo vai trò
    if (!empty($allowedRoles) && !in_array($_SESSION['vai_tro'], $allowedRoles)) {
        header('Location: /appnghiphep_v2/index.php?error=access_denied');
        exit;
    }
    
    return true;
}

// 5. Lấy thông tin user hiện tại (nếu đã đăng nhập)
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = getCurrentUser();
}

// File này KHÔNG có output HTML
// Chỉ xử lý logic và chuẩn bị dữ liệu
?>