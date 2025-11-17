<?php
// views/nhan_vien/dashboard.php - Dashboard nhân viên hành chính

$pageTitle = 'Trang chủ - Nhân viên';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['NHAN_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Lấy thông tin phép năm
$stmtLeave = $pdo->prepare("
    SELECT 
        SoNgayPhepNam,
        SoNgayPhepDaDung,
        SoNgayPhepTonNamTruoc,
        NamPhepTon
    FROM NguoiDung
    WHERE MaNguoiDung = ?
");
$stmtLeave->execute([$userId]);
$leaveInfo = $stmtLeave->fetch();

$totalLeave = $leaveInfo['SoNgayPhepNam'];
$usedLeave = $leaveInfo['SoNgayPhepDaDung'];
$remainingLeave = $totalLeave - $usedLeave;

// Phép năm trước
$oldLeaveAvailable = 0;
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
if ($currentMonth >= 1 && $currentMonth <= 3 && $leaveInfo['NamPhepTon'] == $currentYear - 1) {
    $oldLeaveAvailable = $leaveInfo['SoNgayPhepTonNamTruoc'];
}

// Thống kê đơn
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai IN ('Cho_duyet_cap_1', 'Cho_duyet_cap_2', 'Cho_duyet_cap_3') THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
    FROM DonNghiPhep
    WHERE MaNguoiDung = ?
");
$stmtStats->execute([$userId]);
$stats = $stmtStats->fetch();

// Lấy đơn gần đây
$stmtRecent = $pdo->prepare("
    SELECT 
        d.*,
        CASE 
            WHEN d.LoaiPhep = 'Tinh_phep_nam' THEN lp1.TenLoaiPhep
            ELSE lp2.TenLoaiPhep
        END as TenLoaiPhep
    FROM DonNghiPhep d
    LEFT JOIN LoaiPhepTinhPhepNam lp1 ON d.MaLoaiPhep = lp1.MaLoaiPhep AND d.LoaiPhep = 'Tinh_phep_nam'
    LEFT JOIN LoaiPhepKhongTinhPhepNam lp2 ON d.MaLoaiPhep = lp2.MaLoaiPhep AND d.LoaiPhep = 'Khong_tinh_phep_nam'
    WHERE d.MaNguoiDung = ?
    ORDER BY d.NgayTao DESC
    LIMIT 5
");
$stmtRecent->execute([$userId]);
$recentLeaves = $stmtRecent->fetchAll();

// Nghỉ bù đang chờ
$stmtCompensation = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM NghiBuLamBu
    WHERE MaNguoiDung = ? AND TrangThai = 'Cho_xac_nhan'
");
$stmtCompensation->execute([$userId]);
$compensationPending = $stmtCompensation->fetch()['count'];
?>

<div class="row g-4 mb-4">
    <!-- Card Phép năm -->
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 class="mb-1"><?= formatDayCount($totalLeave) ?></h3>
            <p class="text-muted mb-0">Tổng phép năm</p>
        </div>
    </div>
    
    <!-- Card Đã dùng -->
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="mb-1"><?= formatDayCount($usedLeave) ?></h3>
            <p class="text-muted mb-0">Đã sử dụng</p>
        </div>
    </div>
    
    <!-- Card Còn lại -->
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3 class="mb-1"><?= formatDayCount($remainingLeave) ?></h3>
            <p class="text-muted mb-0">Còn lại</p>
            <?php if ($oldLeaveAvailable > 0): ?>
                <small class="text-success">
                    <i class="fas fa-info-circle"></i>
                    +<?= formatDayCount($oldLeaveAvailable) ?> phép năm trước
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Card Chờ duyệt -->
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h3 class="mb-1"><?= $stats['pending'] ?></h3>
            <p class="text-muted mb-0">Đơn chờ duyệt</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Thống kê đơn -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Thống kê đơn nghỉ phép
                </h5>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng số đơn</span>
                        <strong><?= $stats['total'] ?></strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-clock text-warning me-2"></i>Chờ duyệt</span>
                        <strong><?= $stats['pending'] ?></strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-check-circle text-success me-2"></i>Đã duyệt</span>
                        <strong><?= $stats['approved'] ?></strong>
                    </div>
                </div>
                
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-times-circle text-danger me-2"></i>Từ chối</span>
                        <strong><?= $stats['rejected'] ?></strong>
                    </div>
                </div>
                
                <?php if ($compensationPending > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bạn có <strong><?= $compensationPending ?></strong> đơn nghỉ bù đang chờ xác nhận
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Đơn gần đây -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Đơn nghỉ phép gần đây
                    </h5>
                    <a href="don_cua_toi.php" class="btn btn-sm btn-outline-primary">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <?php if (empty($recentLeaves)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đơn nghỉ phép nào</p>
                        <a href="tao_don.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo đơn mới
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Loại phép</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Số ngày</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLeaves as $leave): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($leave['MaDon']) ?></strong>
                                        </td>
                                        <td><?= e($leave['TenLoaiPhep']) ?></td>
                                        <td>
                                            <?= formatDate($leave['NgayBatDauNghi']) ?> - 
                                            <?= formatDate($leave['NgayKetThucNghi']) ?>
                                        </td>
                                        <td>
                                            <strong><?= formatDayCount($leave['SoNgayNghi']) ?></strong> ngày
                                        </td>
                                        <td><?= getStatusBadge($leave['TrangThai']) ?></td>
                                        <td>
                                            <a href="chi_tiet_don.php?id=<?= $leave['MaDon'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Thao tác nhanh
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="tao_don.php" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                            <br>Tạo đơn nghỉ phép
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="nghi_bu.php" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                            <br>Đăng ký nghỉ bù
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="don_cua_toi.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <br>Xem đơn của tôi
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/appnghiphep_v2/views/profile.php" class="btn btn-secondary w-100 py-3">
                            <i class="fas fa-user-circle fa-2x mb-2"></i>
                            <br>Thông tin cá nhân
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>