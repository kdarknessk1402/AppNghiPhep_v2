<?php
// views/admin/nguoi_dung.php - Quản lý người dùng
require_once __DIR__ . '/../../includes/init.php';
requireAuth(['ADMIN']);
$pageTitle = 'Quản lý người dùng';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mail_config.php';


checkRole(['ADMIN']);

$pdo = getDBConnection();

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    try {
        $maNguoiDung = 'USER' . time();
        $tenDangNhap = trim($_POST['ten_dang_nhap']);
        $hoTen = trim($_POST['ho_ten']);
        $email = trim($_POST['email']);
        $gioiTinh = $_POST['gioi_tinh'];
        $maVaiTro = (int)$_POST['ma_vai_tro'];
        $maKhoaPhong = !empty($_POST['ma_khoa_phong']) ? (int)$_POST['ma_khoa_phong'] : null;
        $ngayBatDau = $_POST['ngay_bat_dau'];
        
        // Validate
        if (empty($tenDangNhap) || empty($hoTen) || empty($email)) {
            throw new Exception('Vui lòng điền đầy đủ thông tin');
        }
        
        // Check username exists
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM NguoiDung WHERE TenDangNhap = ?");
        $stmtCheck->execute([$tenDangNhap]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception('Tên đăng nhập đã tồn tại');
        }
        
        // Check email exists
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM NguoiDung WHERE Email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception('Email đã tồn tại');
        }
        
        // Tạo token kích hoạt
        $token = generateToken();
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO NguoiDung (
                MaNguoiDung, TenDangNhap, HoTen, Email, GioiTinh,
                MaVaiTro, MaKhoaPhong, NgayBatDauLamViec,
                TrangThai, TokenKichHoat, TokenExpiry
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Chua_kich_hoat', ?, ?)
        ");
        
        $stmt->execute([
            $maNguoiDung,
            $tenDangNhap,
            $hoTen,
            $email,
            $gioiTinh,
            $maVaiTro,
            $maKhoaPhong,
            $ngayBatDau,
            $token,
            $tokenExpiry
        ]);
        
        // Gửi email kích hoạt
        sendActivationEmail($email, $hoTen, $token);
        
        logActivity('ADD_USER', "Thêm người dùng: $tenDangNhap ($email)");
        $_SESSION['success'] = 'Thêm người dùng thành công. Email kích hoạt đã được gửi.';
        
        header('Location: nguoi_dung.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Xử lý xóa người dùng
if (isset($_GET['delete']) && isset($_GET['id'])) {
    try {
        $userId = $_GET['id'];
        
        // Không cho xóa chính mình
        if ($userId === $currentUser['ma_nguoi_dung']) {
            throw new Exception('Không thể xóa tài khoản của chính bạn');
        }
        
        $stmt = $pdo->prepare("DELETE FROM NguoiDung WHERE MaNguoiDung = ?");
        $stmt->execute([$userId]);
        
        logActivity('DELETE_USER', "Xóa người dùng: $userId");
        $_SESSION['success'] = 'Xóa người dùng thành công';
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: nguoi_dung.php');
    exit;
}

// Xử lý đổi trạng thái
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    try {
        $userId = $_GET['id'];
        $newStatus = $_GET['status'] === 'Hoat_dong' ? 'Khoa' : 'Hoat_dong';
        
        $stmt = $pdo->prepare("UPDATE NguoiDung SET TrangThai = ? WHERE MaNguoiDung = ?");
        $stmt->execute([$newStatus, $userId]);
        
        logActivity('TOGGLE_USER_STATUS', "Đổi trạng thái người dùng: $userId -> $newStatus");
        $_SESSION['success'] = 'Cập nhật trạng thái thành công';
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: nguoi_dung.php');
    exit;
}

// Lấy danh sách người dùng
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

$whereClause = "1=1";
$params = [];

if ($filter !== 'all') {
    $whereClause .= " AND n.TrangThai = ?";
    $params[] = $filter;
}

if (!empty($search)) {
    $whereClause .= " AND (n.HoTen LIKE ? OR n.Email LIKE ? OR n.TenDangNhap LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$stmt = $pdo->prepare("
    SELECT 
        n.*,
        v.TenVaiTro,
        k.TenKhoaPhong
    FROM NguoiDung n
    JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    WHERE $whereClause
    ORDER BY n.NgayTao DESC
");
$stmt->execute($params);
$users = $stmt->fetchAll();

// Lấy danh sách vai trò và khoa/phòng cho form
$roles = getRoles($pdo);
$departments = getDepartments($pdo);
?>

<!-- Modal thêm người dùng -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Thêm người dùng mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ten_dang_nhap" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ho_ten" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                            <small class="text-muted">Email để gửi link kích hoạt</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select class="form-select" name="gioi_tinh">
                                <option value="Nam">Nam</option>
                                <option value="Nu">Nữ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" name="ma_vai_tro" required>
                                <option value="">-- Chọn vai trò --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['MaVaiTro'] ?>">
                                        <?= e($role['TenVaiTro']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khoa/Phòng</label>
                            <select class="form-select" name="ma_khoa_phong">
                                <option value="">-- Không thuộc khoa/phòng nào --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['MaKhoaPhong'] ?>">
                                        <?= e($dept['TenKhoaPhong']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu làm việc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="ngay_bat_dau" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Sau khi thêm, hệ thống sẽ gửi email kích hoạt tài khoản đến địa chỉ email đã nhập.
                        Người dùng cần kích hoạt tài khoản và tạo mật khẩu trước khi đăng nhập.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="add_user" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Thêm người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Danh sách người dùng -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Quản lý người dùng
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Thêm người dùng
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" class="input-group">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm kiếm theo tên, email, username..."
                           value="<?= e($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="col-md-6">
                <select class="form-select" onchange="window.location.href='?filter='+this.value">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="Hoat_dong" <?= $filter === 'Hoat_dong' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="Chua_kich_hoat" <?= $filter === 'Chua_kich_hoat' ? 'selected' : '' ?>>Chưa kích hoạt</option>
                    <option value="Khoa" <?= $filter === 'Khoa' ? 'selected' : '' ?>>Đã khóa</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                <p class="text-muted">Không tìm thấy người dùng nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>Mã NV</th>
                            <th>Thông tin</th>
                            <th>Vai trò</th>
                            <th>Khoa/Phòng</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong><?= e($user['MaNguoiDung']) ?></strong></td>
                                <td>
                                    <strong><?= e($user['HoTen']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i><?= e($user['TenDangNhap']) ?>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i><?= e($user['Email']) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= e($user['TenVaiTro']) ?>
                                    </span>
                                </td>
                                <td><?= e($user['TenKhoaPhong'] ?? 'N/A') ?></td>
                                <td>
                                    <small><?= formatDate($user['NgayTao'], 'd/m/Y') ?></small>
                                </td>
                                <td>
                                    <?php
                                    $statusBadge = [
                                        'Hoat_dong' => '<span class="badge bg-success">Hoạt động</span>',
                                        'Chua_kich_hoat' => '<span class="badge bg-warning">Chưa kích hoạt</span>',
                                        'Khoa' => '<span class="badge bg-danger">Đã khóa</span>'
                                    ];
                                    echo $statusBadge[$user['TrangThai']] ?? '<span class="badge bg-secondary">N/A</span>';
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($user['MaNguoiDung'] !== $currentUser['ma_nguoi_dung']): ?>
                                            <a href="?toggle_status=1&id=<?= $user['MaNguoiDung'] ?>&status=<?= $user['TrangThai'] ?>" 
                                               class="btn btn-outline-<?= $user['TrangThai'] === 'Hoat_dong' ? 'warning' : 'success' ?>"
                                               onclick="return confirm('Xác nhận đổi trạng thái người dùng này?')"
                                               title="<?= $user['TrangThai'] === 'Hoat_dong' ? 'Khóa' : 'Kích hoạt' ?>">
                                                <i class="fas fa-<?= $user['TrangThai'] === 'Hoat_dong' ? 'lock' : 'unlock' ?>"></i>
                                            </a>
                                            <a href="?delete=1&id=<?= $user['MaNguoiDung'] ?>" 
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('Xác nhận XÓA người dùng này?\n\nHành động này không thể hoàn tác!')"
                                               title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Bạn</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        pageLength: 10,
        order: [[4, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>