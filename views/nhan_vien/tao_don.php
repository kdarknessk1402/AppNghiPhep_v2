<?php
// views/nhan_vien/tao_don.php - Tạo đơn nghỉ phép

$pageTitle = 'Tạo đơn nghỉ phép';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mail_config.php';

checkRole(['NHAN_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Lấy thông tin phép của user
$stmtUser = $pdo->prepare("
    SELECT n.*, k.TenKhoaPhong
    FROM NguoiDung n
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    WHERE n.MaNguoiDung = ?
");
$stmtUser->execute([$userId]);
$userInfo = $stmtUser->fetch();

$totalLeave = $userInfo['SoNgayPhepNam'];
$usedLeave = $userInfo['SoNgayPhepDaDung'];
$remainingLeave = $totalLeave - $usedLeave;

// Phép năm trước
$oldLeaveAvailable = 0;
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
if ($currentMonth >= 1 && $currentMonth <= 3 && $userInfo['NamPhepTon'] == $currentYear - 1) {
    $oldLeaveAvailable = $userInfo['SoNgayPhepTonNamTruoc'];
}

// Lấy danh sách loại phép
$leaveTypesTinh = getLeaveTypesTinhPhep($pdo);
$leaveTypesKhongTinh = getLeaveTypesKhongTinhPhep($pdo);

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $loaiPhep = $_POST['loai_phep']; // 'tinh' hoặc 'khong_tinh'
        $maLoaiPhep = (int)$_POST['ma_loai_phep'];
        $ngayBatDau = $_POST['ngay_bat_dau'];
        $buoiBatDau = $_POST['buoi_bat_dau'];
        $ngayKetThuc = $_POST['ngay_ket_thuc'];
        $buoiKetThuc = $_POST['buoi_ket_thuc'];
        $lyDo = trim($_POST['ly_do']);
        $nguoiBanGiao = trim($_POST['nguoi_ban_giao'] ?? '');
        
        // Validate
        if (empty($ngayBatDau) || empty($ngayKetThuc) || empty($lyDo)) {
            throw new Exception('Vui lòng điền đầy đủ thông tin');
        }
        
        // Tính số ngày nghỉ
        $soNgay = calculateDays($ngayBatDau, $ngayKetThuc, $buoiBatDau, $buoiKetThuc);
        
        if ($soNgay <= 0) {
            throw new Exception('Số ngày nghỉ không hợp lệ');
        }
        
        // Kiểm tra phép còn lại nếu là loại tính phép năm
        if ($loaiPhep === 'tinh') {
            $totalAvailable = $remainingLeave + $oldLeaveAvailable;
            if ($soNgay > $totalAvailable) {
                throw new Exception('Số ngày nghỉ vượt quá số phép còn lại (' . formatDayCount($totalAvailable) . ' ngày)');
            }
        }
        
        // Lấy tên loại phép
        if ($loaiPhep === 'tinh') {
            $stmt = $pdo->prepare("SELECT TenLoaiPhep FROM LoaiPhepTinhPhepNam WHERE MaLoaiPhep = ?");
        } else {
            $stmt = $pdo->prepare("SELECT TenLoaiPhep FROM LoaiPhepKhongTinhPhepNam WHERE MaLoaiPhep = ?");
        }
        $stmt->execute([$maLoaiPhep]);
        $tenLoaiPhep = $stmt->fetchColumn();
        
        // Tạo mã đơn
        $maDon = generateLeaveCode();
        
        // Xác định cấp duyệt đầu tiên
        $trangThai = 'Cho_duyet_cap_1';
        
        // Insert đơn nghỉ phép
        $stmtInsert = $pdo->prepare("
            INSERT INTO DonNghiPhep (
                MaDon, MaNguoiDung, NguoiTao, MaLoaiPhep, 
                LoaiPhep, TenLoaiPhep,
                NgayBatDauNghi, BuoiNghiBatDau,
                NgayKetThucNghi, BuoiNghiKetThuc,
                SoNgayNghi, LyDo, NguoiBanGiaoCongViec,
                TrangThai, CapDuyetHienTai
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?,
                ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, 1
            )
        ");
        
        $stmtInsert->execute([
            $maDon,
            $userId,
            $userId,
            $maLoaiPhep,
            $loaiPhep === 'tinh' ? 'Tinh_phep_nam' : 'Khong_tinh_phep_nam',
            $tenLoaiPhep,
            $ngayBatDau,
            $buoiBatDau,
            $ngayKetThuc,
            $buoiKetThuc,
            $soNgay,
            $lyDo,
            $nguoiBanGiao,
            $trangThai,
        ]);
        
        // Tìm người duyệt cấp 1 (Trưởng phòng)
        $stmtManager = $pdo->prepare("
            SELECT n.Email, n.HoTen
            FROM NguoiDung n
            JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
            WHERE n.MaKhoaPhong = ? AND v.TenVaiTro = 'TRUONG_PHONG'
            LIMIT 1
        ");
        $stmtManager->execute([$userInfo['MaKhoaPhong']]);
        $manager = $stmtManager->fetch();
        
        // Gửi email thông báo
        if ($manager) {
            sendLeaveNotificationLevel1($maDon, $manager['Email']);
        }
        
        // Log hoạt động
        logActivity('CREATE_LEAVE', "Tạo đơn nghỉ phép: $maDon");
        
        $_SESSION['success'] = 'Tạo đơn nghỉ phép thành công! Mã đơn: ' . $maDon;
        header('Location: don_cua_toi.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-medical me-2 text-primary"></i>
                    Tạo đơn nghỉ phép mới
                </h5>
            </div>
            
            <div class="card-body p-4">
                <!-- Thông tin phép năm -->
                <div class="alert alert-info mb-4">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="mb-0"><?= formatDayCount($totalLeave) ?></h4>
                            <small>Tổng phép năm</small>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-0"><?= formatDayCount($usedLeave) ?></h4>
                            <small>Đã sử dụng</small>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-0 text-success"><?= formatDayCount($remainingLeave) ?></h4>
                            <small>Còn lại</small>
                            <?php if ($oldLeaveAvailable > 0): ?>
                                <br><small class="text-success">+<?= formatDayCount($oldLeaveAvailable) ?> phép cũ</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <form method="POST" id="leaveForm" class="needs-validation" novalidate>
                    <!-- Loại phép -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-list-ul me-2"></i>Loại phép <span class="text-danger">*</span>
                        </label>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="loai_phep" 
                                       id="loai_tinh" value="tinh" checked 
                                       onchange="updateLeaveTypes()">
                                <label class="form-check-label" for="loai_tinh">
                                    Phép tính vào phép năm
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="loai_phep" 
                                       id="loai_khong_tinh" value="khong_tinh" 
                                       onchange="updateLeaveTypes()">
                                <label class="form-check-label" for="loai_khong_tinh">
                                    Phép không tính vào phép năm
                                </label>
                            </div>
                        </div>
                        
                        <select class="form-select" name="ma_loai_phep" id="ma_loai_phep" required>
                            <option value="">-- Chọn loại phép --</option>
                            <optgroup label="Tính vào phép năm" id="group_tinh">
                                <?php foreach ($leaveTypesTinh as $type): ?>
                                    <option value="<?= $type['MaLoaiPhep'] ?>">
                                        <?= e($type['TenLoaiPhep']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Không tính vào phép năm" id="group_khong_tinh" style="display:none;">
                                <?php foreach ($leaveTypesKhongTinh as $type): ?>
                                    <option value="<?= $type['MaLoaiPhep'] ?>" 
                                            data-gender="<?= $type['GioiTinh'] ?>">
                                        <?= e($type['TenLoaiPhep']) ?>
                                        <?php if ($type['GioiTinh'] !== 'Tat_ca'): ?>
                                            (<?= formatGender($type['GioiTinh']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                    
                    <!-- Ngày bắt đầu -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="ngay_bat_dau" class="form-label">
                                <i class="fas fa-calendar-plus me-2"></i>Ngày bắt đầu nghỉ <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="ngay_bat_dau" 
                                   name="ngay_bat_dau" required 
                                   min="<?= date('Y-m-d') ?>"
                                   onchange="updateLeaveDays()">
                        </div>
                        <div class="col-md-4">
                            <label for="buoi_bat_dau" class="form-label">Buổi</label>
                            <select class="form-select" id="buoi_bat_dau" name="buoi_bat_dau" onchange="updateLeaveDays()">
                                <option value="ca_ngay">Cả ngày</option>
                                <option value="buoi_sang">Buổi sáng</option>
                                <option value="buoi_chieu">Buổi chiều</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Ngày kết thúc -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="ngay_ket_thuc" class="form-label">
                                <i class="fas fa-calendar-minus me-2"></i>Ngày kết thúc nghỉ <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="ngay_ket_thuc" 
                                   name="ngay_ket_thuc" required 
                                   min="<?= date('Y-m-d') ?>"
                                   onchange="updateLeaveDays()">
                        </div>
                        <div class="col-md-4">
                            <label for="buoi_ket_thuc" class="form-label">Buổi</label>
                            <select class="form-select" id="buoi_ket_thuc" name="buoi_ket_thuc" onchange="updateLeaveDays()">
                                <option value="ca_ngay">Cả ngày</option>
                                <option value="buoi_sang">Buổi sáng</option>
                                <option value="buoi_chieu">Buổi chiều</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Số ngày nghỉ (tính tự động) -->
                    <div class="alert alert-success mb-3">
                        <strong>
                            <i class="fas fa-calculator me-2"></i>Số ngày nghỉ: 
                            <span id="so_ngay_nghi_display" class="text-primary">0</span> ngày
                        </strong>
                    </div>
                    
                    <!-- Lý do -->
                    <div class="mb-3">
                        <label for="ly_do" class="form-label">
                            <i class="fas fa-comment-dots me-2"></i>Lý do nghỉ <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="ly_do" name="ly_do" 
                                  rows="4" required 
                                  placeholder="Nhập lý do nghỉ phép..."></textarea>
                    </div>
                    
                    <!-- Người bàn giao công việc -->
                    <div class="mb-4">
                        <label for="nguoi_ban_giao" class="form-label">
                            <i class="fas fa-user-friends me-2"></i>Người được bàn giao công việc
                        </label>
                        <input type="text" class="form-control" id="nguoi_ban_giao" 
                               name="nguoi_ban_giao" 
                               placeholder="Tên người được bàn giao (không bắt buộc)">
                        <small class="text-muted">Có thể để trống</small>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Gửi đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Cập nhật danh sách loại phép
function updateLeaveTypes() {
    const loaiPhep = document.querySelector('input[name="loai_phep"]:checked').value;
    const groupTinh = document.getElementById('group_tinh');
    const groupKhongTinh = document.getElementById('group_khong_tinh');
    const selectLoaiPhep = document.getElementById('ma_loai_phep');
    
    if (loaiPhep === 'tinh') {
        groupTinh.style.display = '';
        groupKhongTinh.style.display = 'none';
    } else {
        groupTinh.style.display = 'none';
        groupKhongTinh.style.display = '';
    }
    
    selectLoaiPhep.value = '';
}

// Cập nhật số ngày nghỉ tự động
updateLeaveDays();

// Validate ngày kết thúc phải >= ngày bắt đầu
document.getElementById('ngay_bat_dau').addEventListener('change', function() {
    document.getElementById('ngay_ket_thuc').min = this.value;
});

// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>