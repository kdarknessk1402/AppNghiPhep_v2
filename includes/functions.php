<?php
// includes/functions.php - Các hàm tiện ích

/**
 * Format ngày tháng
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Format số ngày nghỉ
 */
function formatDayCount($days) {
    return number_format($days, 1);
}

/**
 * Tính số ngày giữa 2 ngày (bao gồm nửa ngày)
 */
function calculateDays($startDate, $endDate, $startSession = 'ca_ngay', $endSession = 'ca_ngay') {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $diff = $start->diff($end)->days + 1; // +1 để bao gồm cả 2 ngày
    
    // Trừ đi nếu là nửa ngày
    if ($startSession === 'buoi_sang') {
        $diff -= 0.5;
    } elseif ($startSession === 'buoi_chieu') {
        $diff -= 0.5;
    }
    
    if ($endSession === 'buoi_sang') {
        $diff -= 0.5;
    } elseif ($endSession === 'buoi_chieu') {
        $diff -= 0.5;
    }
    
    return max(0, $diff);
}

/**
 * Lấy trạng thái đơn với màu sắc
 */
function getStatusBadge($status) {
    $badges = [
        'Cho_duyet_cap_1' => '<span class="badge bg-warning">Chờ duyệt cấp 1</span>',
        'Cho_duyet_cap_2' => '<span class="badge bg-info">Chờ duyệt cấp 2</span>',
        'Cho_duyet_cap_3' => '<span class="badge bg-primary">Chờ duyệt cấp 3</span>',
        'Da_duyet' => '<span class="badge bg-success">Đã duyệt</span>',
        'Tu_choi' => '<span class="badge bg-danger">Từ chối</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Không xác định</span>';
}

/**
 * Tạo mã đơn nghỉ phép
 */
function generateLeaveCode() {
    return 'DN' . date('YmdHis') . rand(1000, 9999);
}

/**
 * Tạo mã nghỉ bù
 */
function generateCompensationCode() {
    return 'NB' . date('YmdHis') . rand(1000, 9999);
}

/**
 * Kiểm tra email hợp lệ
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Kiểm tra mật khẩu
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Mã hóa mật khẩu
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Lấy danh sách loại phép tính phép năm
 */
function getLeaveTypesTinhPhep($pdo) {
    $stmt = $pdo->query("
        SELECT MaLoaiPhep, TenLoaiPhep 
        FROM LoaiPhepTinhPhepNam 
        WHERE TrangThai = 'Hoat_dong'
        ORDER BY TenLoaiPhep
    ");
    return $stmt->fetchAll();
}

/**
 * Lấy danh sách loại phép không tính phép năm
 */
function getLeaveTypesKhongTinhPhep($pdo) {
    $stmt = $pdo->query("
        SELECT MaLoaiPhep, TenLoaiPhep, GioiTinh
        FROM LoaiPhepKhongTinhPhepNam 
        WHERE TrangThai = 'Hoat_dong'
        ORDER BY TenLoaiPhep
    ");
    return $stmt->fetchAll();
}

/**
 * Lấy thông tin khoa/phòng
 */
function getDepartments($pdo) {
    $stmt = $pdo->query("
        SELECT MaKhoaPhong, TenKhoaPhong, LoaiDonVi
        FROM KhoaPhong 
        WHERE TrangThai = 'Hoat_dong'
        ORDER BY TenKhoaPhong
    ");
    return $stmt->fetchAll();
}

/**
 * Lấy thông tin vai trò
 */
function getRoles($pdo) {
    $stmt = $pdo->query("
        SELECT MaVaiTro, TenVaiTro, MoTa
        FROM VaiTro 
        WHERE TrangThai = 1
        ORDER BY MaVaiTro
    ");
    return $stmt->fetchAll();
}

/**
 * Tính số ngày phép năm theo thâm niên
 */
function calculateAnnualLeave($startDate) {
    $start = new DateTime($startDate);
    $now = new DateTime();
    $months = $start->diff($now)->m + ($start->diff($now)->y * 12);
    
    if ($months < 12) {
        // Chưa đủ 12 tháng: 1 tháng = 1 ngày phép
        return $months;
    }
    
    // Đủ 12 tháng: 12 ngày phép cơ bản
    $baseLeave = 12;
    
    // Cứ 60 tháng (5 năm) thêm 1 ngày phép
    $seniorityBonus = floor($months / 60);
    
    return $baseLeave + $seniorityBonus;
}

/**
 * Kiểm tra phép năm cũ còn dùng được không (T1-T3)
 */
function canUseOldLeave($year) {
    $currentMonth = (int)date('n');
    $currentYear = (int)date('Y');
    
    // Chỉ được dùng từ T1-T3 và phải là phép của năm trước
    return ($currentMonth >= 1 && $currentMonth <= 3) && ($year == $currentYear - 1);
}

/**
 * Upload file
 */
function uploadFile($file, $targetDir = 'uploads/') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }
    
    $uploadDir = __DIR__ . '/../' . $targetDir;
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetDir . $filename;
    }
    
    return null;
}

/**
 * Xóa file
 */
function deleteFile($filePath) {
    $fullPath = __DIR__ . '/../' . $filePath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Tạo token ngẫu nhiên
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Format giới tính
 */
function formatGender($gender) {
    return $gender === 'Nam' ? 'Nam' : 'Nữ';
}

/**
 * Lấy tên tháng tiếng Việt
 */
function getVietnameseMonth($month) {
    $months = [
        1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3',
        4 => 'Tháng 4', 5 => 'Tháng 5', 6 => 'Tháng 6',
        7 => 'Tháng 7', 8 => 'Tháng 8', 9 => 'Tháng 9',
        10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
    ];
    return $months[(int)$month] ?? '';
}
?>

