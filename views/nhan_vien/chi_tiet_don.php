<?php
// views/nhan_vien/chi_tiet_don.php - Chi tiết đơn nghỉ phép

$pageTitle = 'Chi tiết đơn nghỉ phép';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['NHAN_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];
$maDon = $_GET['id'] ?? '';

if (empty($maDon)) {
    $_SESSION['error'] = 'Không tìm thấy đơn';
    header('Location: don_cua_toi.php');
    exit;
}

// Lấy thông tin đơn
$stmt = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email, n.GioiTinh,
        k.TenKhoaPhong,
        u1.HoTen as NguoiDuyetCap1Ten,
        u2.HoTen as NguoiDuyetCap2Ten,
        u3.HoTen as NguoiDuyetCap3Ten
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    LEFT JOIN NguoiDung u1 ON d.NguoiDuyetCap1 = u1.MaNguoiDung
    LEFT JOIN NguoiDung u2 ON d.NguoiDuyetCap2 = u2.MaNguoiDung
    LEFT JOIN NguoiDung u3 ON d.NguoiDuyetCap3 = u3.MaNguoiDung
    WHERE d.MaDon = ? AND d.MaNguoiDung = ?
");
$stmt->execute([$maDon, $userId]);
$leave = $stmt->fetch();

if (!$leave) {
    $_SESSION['error'] = 'Không tìm thấy đơn hoặc bạn không có quyền xem';
    header('Location: don_cua_toi.php');
    exit;
}

// Xác định màu trạng thái
$statusColors = [
    'Cho_duyet_cap_1' => 'warning',
    'Cho_duyet_cap_2' => 'info',
    'Cho_duyet_cap_3' => 'primary',
    'Da_duyet' => 'success',
    'Tu_choi' => 'danger'
];
$statusColor = $statusColors[$leave['TrangThai']] ?? 'secondary';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-<?= $statusColor ?> text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Chi tiết đơn nghỉ phép
                        </h5>
                        <small>Mã đơn: <?= e($leave['MaDon']) ?></small>
                    </div>
                    <div>
                        <?= getStatusBadge($leave['TrangThai']) ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4">
                <!-- Thông tin người xin nghỉ -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2 text-primary"></i>
                            Thông tin người xin nghỉ
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150"><strong>Họ tên:</strong></td>
                                <td><?= e($leave['HoTen']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?= e($leave['Email']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Giới tính:</strong></td>
                                <td><?= formatGender($leave['GioiTinh']) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150"><strong>Khoa/Phòng:</strong></td>
                                <td><?= e($leave['TenKhoaPhong'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày tạo đơn:</strong></td>
                                <td><?= formatDate($leave['NgayTao'], 'd/m/Y H:i') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Thông tin nghỉ phép -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Thông tin nghỉ phép
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150"><strong>Loại phép:</strong></td>
                                <td>
                                    <?= e($leave['TenLoaiPhep']) ?>
                                    <br>
                                    <small class="badge bg-<?= $leave['LoaiPhep'] === 'Tinh_phep_nam' ? 'info' : 'secondary' ?>">
                                        <?= $leave['LoaiPhep'] === 'Tinh_phep_nam' ? 'Tính vào phép năm' : 'Không tính vào phép năm' ?>
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ngày bắt đầu:</strong></td>
                                <td>
                                    <?= formatDate($leave['NgayBatDauNghi']) ?>
                                    <?php if ($leave['BuoiNghiBatDau'] !== 'Ca_ngay'): ?>
                                        <span class="badge bg-secondary">
                                            <?= $leave['BuoiNghiBatDau'] === 'Buoi_sang' ? 'Buổi sáng' : 'Buổi chiều' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ngày kết thúc:</strong></td>
                                <td>
                                    <?= formatDate($leave['NgayKetThucNghi']) ?>
                                    <?php if ($leave['BuoiNghiKetThuc'] !== 'Ca_ngay'): ?>
                                        <span class="badge bg-secondary">
                                            <?= $leave['BuoiNghiKetThuc'] === 'Buoi_sang' ? 'Buổi sáng' : 'Buổi chiều' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="180"><strong>Số ngày nghỉ:</strong></td>
                                <td>
                                    <h4 class="text-primary mb-0"><?= formatDayCount($leave['SoNgayNghi']) ?> ngày</h4>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Người bàn giao:</strong></td>
                                <td><?= e($leave['NguoiBanGiaoCongViec'] ?? 'Không có') ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <strong>Lý do nghỉ:</strong>
                        <div class="alert alert-light mt-2">
                            <?= nl2br(e($leave['LyDo'])) ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tiến trình duyệt -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tasks me-2 text-primary"></i>
                            Tiến trình duyệt
                        </h6>
                    </div>
                    <div class="col-md-12">
                        <div class="timeline">
                            <!-- Cấp 1 -->
                            <div class="timeline-item">
                                <div class="timeline-badge bg-<?= in_array($leave['TrangThaiCap1'], ['Dong_y']) ? 'success' : ($leave['TrangThaiCap1'] === 'Tu_choi' ? 'danger' : 'secondary') ?>">
                                    <i class="fas fa-<?= $leave['TrangThaiCap1'] === 'Dong_y' ? 'check' : ($leave['TrangThaiCap1'] === 'Tu_choi' ? 'times' : 'clock') ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Cấp 1 - Trưởng Khoa/Phòng</h6>
                                    <?php if ($leave['NguoiDuyetCap1']): ?>
                                        <p class="mb-1">
                                            <strong>Người duyệt:</strong> <?= e($leave['NguoiDuyetCap1Ten']) ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Ngày duyệt:</strong> <?= formatDate($leave['NgayDuyetCap1'], 'd/m/Y H:i') ?>
                                        </p>
                                        <?php if ($leave['GhiChuCap1']): ?>
                                            <p class="mb-0">
                                                <strong>Ghi chú:</strong> <?= e($leave['GhiChuCap1']) ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Đang chờ xử lý</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Cấp 2 -->
                            <?php if ($leave['CapDuyetHienTai'] >= 2): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-<?= in_array($leave['TrangThaiCap2'], ['Dong_y']) ? 'success' : ($leave['TrangThaiCap2'] === 'Tu_choi' ? 'danger' : 'secondary') ?>">
                                    <i class="fas fa-<?= $leave['TrangThaiCap2'] === 'Dong_y' ? 'check' : ($leave['TrangThaiCap2'] === 'Tu_choi' ? 'times' : 'clock') ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Cấp 2 - Phó Hiệu Trưởng</h6>
                                    <?php if ($leave['NguoiDuyetCap2']): ?>
                                        <p class="mb-1">
                                            <strong>Người duyệt:</strong> <?= e($leave['NguoiDuyetCap2Ten']) ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Ngày duyệt:</strong> <?= formatDate($leave['NgayDuyetCap2'], 'd/m/Y H:i') ?>
                                        </p>
                                        <?php if ($leave['GhiChuCap2']): ?>
                                            <p class="mb-0">
                                                <strong>Ghi chú:</strong> <?= e($leave['GhiChuCap2']) ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Đang chờ xử lý</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Cấp 3 -->
                            <?php if ($leave['CapDuyetHienTai'] >= 3): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-<?= in_array($leave['TrangThaiCap3'], ['Dong_y']) ? 'success' : ($leave['TrangThaiCap3'] === 'Tu_choi' ? 'danger' : 'secondary') ?>">
                                    <i class="fas fa-<?= $leave['TrangThaiCap3'] === 'Dong_y' ? 'check' : ($leave['TrangThaiCap3'] === 'Tu_choi' ? 'times' : 'clock') ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Cấp 3 - Hiệu Trưởng (Quyết định cuối)</h6>
                                    <?php if ($leave['NguoiDuyetCap3']): ?>
                                        <p class="mb-1">
                                            <strong>Người duyệt:</strong> <?= e($leave['NguoiDuyetCap3Ten']) ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Ngày duyệt:</strong> <?= formatDate($leave['NgayDuyetCap3'], 'd/m/Y H:i') ?>
                                        </p>
                                        <?php if ($leave['GhiChuCap3']): ?>
                                            <p class="mb-0">
                                                <strong>Ghi chú:</strong> <?= e($leave['GhiChuCap3']) ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Đang chờ xử lý</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex gap-2 justify-content-between">
                    <a href="don_cua_toi.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-2"></i>In đơn
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline styles */
.timeline {
    position: relative;
    padding-left: 50px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-badge {
    position: absolute;
    left: -30px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.timeline-content h6 {
    margin-bottom: 10px;
    color: #333;
}

@media print {
    .btn, .no-print {
        display: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>