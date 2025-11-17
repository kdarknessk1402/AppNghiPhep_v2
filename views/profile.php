<?php
// views/profile.php - Trang thông tin cá nhân

$pageTitle = 'Thông tin cá nhân';
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    try {
        $email = trim($_POST['email']);
        $appPassword = trim($_POST['app_password'] ?? '');
        
        if (!isValidEmail($email)) {
            throw new Exception('Email không hợp lệ');
        }
        
        $stmt = $pdo->prepare("
            UPDATE NguoiDung 
            SET Email = ?, AppPassword = ?
            WHERE MaNguoiDung = ?
        ");
        $stmt->execute([$email, $appPassword, $userId]);
        
        $_SESSION['email'] = $email;
        $_SESSION['success'] = 'Cập nhật thông tin thành công';
        
        logActivity('UPDATE_PROFILE', 'Cập nhật thông tin cá nhân');
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $authController = new AuthController();
    $result = $authController->changePassword(
        $userId,
        $_POST['old_password'],
        $_POST['new_password'],
        $_POST['confirm_password']
    );
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

// Lấy thông tin user
$stmt = $pdo->prepare("
    SELECT n.*, v.TenVaiTro, k.TenKhoaPhong
    FROM NguoiDung n
    JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    WHERE n.MaNguoiDung = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<div class="row">
    <div class="col-lg-4">
        <!-- Card thông tin cơ bản -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 48px;">
                    <?= strtoupper(mb_substr($user['HoTen'], 0, 1)) ?>
                </div>
                <h4><?= e($user['HoTen']) ?></h4>
                <p class="text-muted mb-2"><?= e($user['TenVaiTro']) ?></p>
                <p class="text-muted mb-0">
                    <i class="fas fa-building me-2"></i>
                    <?= e($user['TenKhoaPhong'] ?? 'Chưa xác định') ?>
                </p>
            </div>
        </div>
        
        <!-- Card thống kê phép (chỉ cho nhân viên/giảng viên) -->
        <?php if (in_array($user['TenVaiTro'], ['NHAN_VIEN', 'GIANG_VIEN'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        Thông tin phép năm
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng phép năm:</span>
                            <strong><?= formatDayCount($user['SoNgayPhepNam']) ?></strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: 100%">
                                <?= formatDayCount($user['SoNgayPhepNam']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đã sử dụng:</span>
                            <strong class="text-warning"><?= formatDayCount($user['SoNgayPhepDaDung']) ?></strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: <?= ($user['SoNgayPhepDaDung'] / $user['SoNgayPhepNam']) * 100 ?>%">
                                <?= formatDayCount($user['SoNgayPhepDaDung']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Còn lại:</span>
                            <strong class="text-success">
                                <?= formatDayCount($user['SoNgayPhepNam'] - $user['SoNgayPhepDaDung']) ?>
                            </strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= (($user['SoNgayPhepNam'] - $user['SoNgayPhepDaDung']) / $user['SoNgayPhepNam']) * 100 ?>%">
                                <?= formatDayCount($user['SoNgayPhepNam'] - $user['SoNgayPhepDaDung']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($user['SoNgayPhepTonNamTruoc'] > 0): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Phép năm trước còn: <strong><?= formatDayCount($user['SoNgayPhepTonNamTruoc']) ?></strong>
                                <br>(Sử dụng trước 31/03)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-8">
        <!-- Thông tin chi tiết -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin chi tiết
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Mã nhân viên:</strong></td>
                                <td><?= e($user['MaNguoiDung']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tên đăng nhập:</strong></td>
                                <td><?= e($user['TenDangNhap']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Họ tên:</strong></td>
                                <td><?= e($user['HoTen']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?= e($user['Email']) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Giới tính:</strong></td>
                                <td><?= formatGender($user['GioiTinh']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td><?= e($user['SoDienThoai'] ?? 'Chưa cập nhật') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày bắt đầu:</strong></td>
                                <td><?= formatDate($user['NgayBatDauLamViec']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    <span class="badge bg-<?= $user['TrangThai'] === 'Hoat_dong' ? 'success' : 'secondary' ?>">
                                        <?= $user['TrangThai'] === 'Hoat_dong' ? 'Đang hoạt động' : 'Không hoạt động' ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cập nhật thông tin -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Cập nhật thông tin
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= e($user['Email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                App Password 
                                <i class="fas fa-question-circle text-muted" 
                                   data-bs-toggle="tooltip" 
                                   title="Mật khẩu ứng dụng cho email (nếu có)"></i>
                            </label>
                            <input type="text" class="form-control" name="app_password" 
                                   value="<?= e($user['AppPassword'] ?? '') ?>" 
                                   placeholder="Nhập app password (tùy chọn)">
                            <p>Hướng dẫn tạo app password từ mail nguoidung@cdntphcm.edu.vn, bấm vào <a href="../assets/misc/Guide.html" target="_blank">đây</a>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_info" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Cập nhật thông tin
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Đổi mật khẩu -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-key me-2"></i>
                    Đổi mật khẩu
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="changePasswordForm">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu cũ <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" 
                               id="new_password" required minlength="6">
                        <small class="text-muted">Tối thiểu 6 ký tự</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" 
                               id="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-warning">
                        <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validate mật khẩu khớp
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu mới và xác nhận mật khẩu không khớp!');
    }
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>