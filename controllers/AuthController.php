<?php
// controllers/AuthController.php - Xử lý xác thực người dùng

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';

class AuthController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Đăng nhập
     */
    public function login($tenDangNhap, $matKhau) {
        try {
            // Tìm user trong database
            $stmt = $this->pdo->prepare("
                SELECT n.*, v.TenVaiTro, v.CapDuyet
                FROM NguoiDung n
                JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
                WHERE n.TenDangNhap = ? AND n.TrangThai = 'Hoat_dong'
            ");
            $stmt->execute([$tenDangNhap]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Tên đăng nhập không tồn tại hoặc tài khoản chưa được kích hoạt'
                ];
            }
            
            // Kiểm tra mật khẩu
            if (!password_verify($matKhau, $user['MatKhau'])) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu không chính xác'
                ];
            }
            
            // Lưu thông tin vào session
            $_SESSION['user_id'] = $user['MaNguoiDung'];
            $_SESSION['ten_dang_nhap'] = $user['TenDangNhap'];
            $_SESSION['ho_ten'] = $user['HoTen'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['vai_tro'] = $user['TenVaiTro'];
            $_SESSION['cap_duyet'] = $user['CapDuyet'];
            $_SESSION['ma_khoa_phong'] = $user['MaKhoaPhong'];
            
            // Log hoạt động
            logActivity('LOGIN', 'Đăng nhập thành công');
            
            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'role' => $user['TenVaiTro']
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng nhập'
            ];
        }
    }
    
    /**
     * Kích hoạt tài khoản với token
     */
    public function activateAccount($token, $matKhau, $matKhauXacNhan) {
        try {
            // Validate mật khẩu
            if (strlen($matKhau) < 6) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
                ];
            }
            
            if ($matKhau !== $matKhauXacNhan) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu xác nhận không khớp'
                ];
            }
            
            // Tìm user với token
            $stmt = $this->pdo->prepare("
                SELECT * FROM NguoiDung 
                WHERE TokenKichHoat = ? 
                AND TokenExpiry > NOW()
                AND TrangThai = 'Chua_kich_hoat'
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Link kích hoạt không hợp lệ hoặc đã hết hạn'
                ];
            }
            
            // Cập nhật mật khẩu và kích hoạt tài khoản
            $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                UPDATE NguoiDung 
                SET MatKhau = ?,
                    TrangThai = 'Hoat_dong',
                    TokenKichHoat = NULL,
                    TokenExpiry = NULL
                WHERE MaNguoiDung = ?
            ");
            $stmt->execute([$hashedPassword, $user['MaNguoiDung']]);
            
            return [
                'success' => true,
                'message' => 'Kích hoạt tài khoản thành công. Bạn có thể đăng nhập ngay bây giờ.'
            ];
            
        } catch (Exception $e) {
            error_log("Activate account error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kích hoạt tài khoản'
            ];
        }
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword($userId, $oldPassword, $newPassword, $confirmPassword) {
        try {
            // Validate
            if ($newPassword !== $confirmPassword) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu mới không khớp'
                ];
            }
            
            if (strlen($newPassword) < 6) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'
                ];
            }
            
            // Kiểm tra mật khẩu cũ
            $stmt = $this->pdo->prepare("SELECT MatKhau FROM NguoiDung WHERE MaNguoiDung = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($oldPassword, $user['MatKhau'])) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu cũ không chính xác'
                ];
            }
            
            // Cập nhật mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE NguoiDung SET MatKhau = ? WHERE MaNguoiDung = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            logActivity('CHANGE_PASSWORD', 'Đổi mật khẩu thành công');
            
            return [
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ];
            
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đổi mật khẩu'
            ];
        }
    }
}
?>