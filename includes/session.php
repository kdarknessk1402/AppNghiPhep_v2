<?php
// includes/session.php - Quản lý session và xác thực

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['vai_tro']);
}

/**
 * Kiểm tra quyền truy cập theo vai trò
 */
function checkRole($allowedRoles = []) {
    if (!isLoggedIn()) {
        header('Location: /appnghiphep_v2/views/auth/login.php');
        exit;
    }
    
    if (!empty($allowedRoles) && !in_array($_SESSION['vai_tro'], $allowedRoles)) {
        header('Location: /appnghiphep_v2/index.php?error=access_denied');
        exit;
    }
    
    return true;
}

/**
 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'ma_nguoi_dung' => $_SESSION['user_id'],
        'ho_ten' => $_SESSION['ho_ten'],
        'email' => $_SESSION['email'],
        'vai_tro' => $_SESSION['vai_tro'],
        'ma_khoa_phong' => $_SESSION['ma_khoa_phong'] ?? null
    ];
}

/**
 * Đăng xuất
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: /appnghiphep_v2/views/auth/login.php');
    exit;
}

/**
 * Lưu log hoạt động
 */
function logActivity($action, $description = '') {
    if (!isLoggedIn()) return;
    
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO LogHoatDong (MaNguoiDung, HanhDong, MoTa, IpAddress, UserAgent)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    } catch (Exception $e) {
        error_log("Log activity error: " . $e->getMessage());
    }
}

/**
 * Redirect theo vai trò
 */
function redirectByRole() {
    if (!isLoggedIn()) {
        header('Location: /appnghiphep_v2/views/auth/login.php');
        exit;
    }
    
    $role = $_SESSION['vai_tro'];
    
    switch ($role) {
        case 'ADMIN':
        case 'HIEU_TRUONG':
            header('Location: /appnghiphep_v2/views/admin/dashboard.php');
            break;
        case 'PHO_HIEU_TRUONG':
            header('Location: /appnghiphep_v2/views/pho_hieu_truong/dashboard.php');
            break;
        case 'TRUONG_PHONG':
            header('Location: /appnghiphep_v2/views/truong_phong/dashboard.php');
            break;
        case 'NHAN_VIEN':
            header('Location: /appnghiphep_v2/views/nhan_vien/dashboard.php');
            break;
        case 'GIANG_VIEN':
            header('Location: /appnghiphep_v2/views/giang_vien/dashboard.php');
            break;
        case 'GIAO_VU_KHOA':
            header('Location: /appnghiphep_v2/views/giao_vu/dashboard.php');
            break;
        default:
            header('Location: /appnghiphep_v2/views/auth/login.php');
            break;
    }
    exit;
}
?>