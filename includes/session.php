<?php
// includes/session.php - Quản lý session và các hàm liên quan

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo có kết nối PDO dùng chung nếu file này được include độc lập
if (!isset($pdo) && function_exists('getDBConnection')) {
    $pdo = getDBConnection();
}

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['vai_tro']);
}

/**
 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, v.TenVaiTro, v.MoTa as MoTaVaiTro
            FROM NguoiDung u
            LEFT JOIN VaiTro v ON u.MaVaiTro = v.MaVaiTro
            WHERE u.MaNguoiDung = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getCurrentUser error: " . $e->getMessage());
        return null;
    }
}

/**
 * Ghi log hoạt động
 */
function logActivity($action, $details = '', $userId = null) {
    global $pdo;
    
    // Ưu tiên user được truyền vào, nếu không lấy từ session
    if ($userId === null) {
        $userId = $_SESSION['user_id'] ?? null;
    }
    
    if (empty($userId)) {
        return false;
    }
    
    try {
        // Kiểm tra xem bảng NhatKyHoatDong có tồn tại không
        $checkTable = $pdo->query("SHOW TABLES LIKE 'NhatKyHoatDong'");
        if ($checkTable->rowCount() == 0) {
            // Bảng không tồn tại, bỏ qua việc log
            return true;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO NhatKyHoatDong (MaNguoiDung, HanhDong, ChiTiet, ThoiGian, DiaChiIP)
            VALUES (?, ?, ?, NOW(), ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt->execute([$userId, $action, $details, $ip]);
        return true;
    } catch (PDOException $e) {
        error_log("logActivity error: " . $e->getMessage());
        return false;
    }
}

/**
 * Kiểm tra quyền truy cập - KHÔNG tự động redirect
 * Chỉ trả về true/false để hàm requireAuth() xử lý
 */
function checkRole($allowedRoles = []) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (!empty($allowedRoles) && !in_array($_SESSION['vai_tro'], $allowedRoles)) {
        return false;
    }
    
    return true;
}

/**
 * Đăng xuất
 */
function logout() {
    if (isset($_SESSION['user_id'])) {
        logActivity('LOGOUT', 'Đăng xuất khỏi hệ thống', $_SESSION['user_id']);
    }
    
    session_destroy();
    header('Location: /appnghiphep_v2/views/auth/login.php');
    exit;
}

/**
 * Format ngày giờ Việt Nam
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Redirect user theo vai trò sau khi đăng nhập
 */
function redirectByRole($role = null) {
    if ($role === null) {
        $role = $_SESSION['vai_tro'] ?? null;
    }
    
    $redirectMap = [
        'admin' => '/appnghiphep_v2/views/admin/dashboard.php',
        'giang_vien' => '/appnghiphep_v2/views/giang_vien/dashboard.php',
        'hieu_truong' => '/appnghiphep_v2/views/hieu_truong/dashboard.php',
        'pho_hieu_truong' => '/appnghiphep_v2/views/pho_hieu_truong/dashboard.php',
        'truong_phong' => '/appnghiphep_v2/views/truong_phong/dashboard.php',
        'nhan_vien' => '/appnghiphep_v2/views/nhan_vien/dashboard.php',
        'manager' => '/appnghiphep_v2/views/truong_phong/dashboard.php',
        'user' => '/appnghiphep_v2/views/nhan_vien/dashboard.php'
    ];
    
    $roleKey = $role ? strtolower($role) : null;
    $url = $roleKey && isset($redirectMap[$roleKey])
        ? $redirectMap[$roleKey]
        : '/appnghiphep_v2/index.php';
    
    header('Location: ' . $url);
    exit;
}