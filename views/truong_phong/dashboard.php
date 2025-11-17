<?php
// views/truong_phong/dashboard.php - Dashboard Trưởng phòng

$pageTitle = 'Trang chủ - Trưởng Khoa/Phòng';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['TRUONG_PHONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];
$khoaPhongId = $currentUser['ma_khoa_phong'];

// Thống kê đơn chờ duyệt cấp 1
$stmtPending = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE n.MaKhoaPhong = ? 
    AND d.TrangThai = 'Cho_duyet_cap_1'
    AND d.CapDuyetHienTai = 1
");
$stmtPending->execute([$khoaPhongId]);
$pendingCount = $stmtPending->fetch()['count'];

// Thống kê tổng đơn trong khoa/phòng
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN d.TrangThai IN ('Cho_duyet_cap_1', 'Cho_duyet_cap_2', 'Cho_duyet_cap_3') THEN 1 ELSE 0 END) as pending_all,
        SUM(CASE WHEN d.TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN d.TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE n.MaKhoaPhong = ?
    AND YEAR(d.NgayTao) = YEAR(CURDATE())
");
$stmtStats->execute([$khoaPhongId]);
$stats = $stmtStats->fetch();

// Thống kê nhân viên trong khoa/phòng
$stmtEmployees = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM NguoiDung
    WHERE MaKhoaPhong = ? AND TrangThai = 'Hoat_dong'
");
$stmtEmployees->execute([$khoaPhongId]);
$employeeCount = $stmtEmployees->fetch()['count'];

// Lấy danh sách đơn chờ duyệt
$stmtLeaves = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email, n.GioiTinh
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE n.MaKhoaPhong = ?
    AND d.TrangThai = 'Cho_duyet_cap_1'
    AND d.CapDuyetHienTai = 1
    ORDER BY d.NgayTao DESC
    LIMIT 10
");
$stmtLeaves->execute([$khoaPhongId]);
$pendingLeaves = $stmtLeaves->fetchAll();

// Đơn đã duyệt gần đây
$stmtApproved = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE n.MaKhoaPhong = ?
    AND d.NguoiDuyetCap1 = ?
    ORDER BY d.NgayDuyetCap1 DESC
    LIMIT 5
");
$stmtApproved->execute([$khoaPhongId, $userId]);
$recentApproved = $stmtApproved->fetchAll();
?>

<div class="row g-4 mb-4">
    <!-- Card Chờ duyệt -->
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="mb-1"><?= $pendingCount ?></h3>
            <p class="text-muted mb-0">Đơn chờ duyệt</p>
            <?php if ($pendingCount > 0): ?>
                <a href="duyet_don.php" class="stretched-link"></a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Card Tổng nhân viên -->
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="mb-1"><?= $employeeCount ?></h3>
            <p class="text-muted mb-0">Nhân viên</p>
        </div>
    </div>
    
    <!-- Card Đã duyệt -->
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="mb-1"><?= $stats['approved'] ?></h3>
            <p class="text-muted mb-0">Đã duyệt (năm nay)</p>
        </div>
    </div>
    
    <!-- Card Từ chối -->
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h3 class="mb-1"><?= $stats['rejected'] ?></h3>
            <p class="text-muted mb-0">Từ chối (năm nay)</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Đơn chờ duyệt -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half me-2 text-warning"></i>
                        Đơn chờ duyệt
                    </h5>
                    <?php if ($pendingCount > 0): ?>
                        <a href="duyet_don.php" class="btn btn-sm btn-warning text-white">
                            Xem tất cả <span class="badge bg-white text-warning"><?= $pendingCount ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($pendingLeaves)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p class="text-muted">Không có đơn nào chờ duyệt</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Loại phép</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Số ngày</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingLeaves as $leave): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($leave['HoTen']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= e($leave['Email']) ?></small>
                                        </td>
                                        <td>
                                            <?= e($leave['TenLoaiPhep']) ?>
                                            <br>
                                            <small class="text-muted"><?= formatDate($leave['NgayTao'], 'd/m/Y H:i') ?></small>
                                        </td>
                                        <td>
                                            <?= formatDate($leave['NgayBatDauNghi']) ?>
                                            <br>
                                            <i class="fas fa-arrow-down small text-muted"></i>
                                            <br>
                                            <?= formatDate($leave['NgayKetThucNghi']) ?>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                            <br>
                                            <small class="text-muted">ngày</small>
                                        </td>
                                        <td>
                                            <a href="duyet_don.php?action=view&id=<?= $leave['MaDon'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Xem và duyệt">
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
    
    <!-- Hoạt động gần đây -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Đã duyệt gần đây
                </h5>
            </div>
            
            <div class="card-body">
                <?php if (empty($recentApproved)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Chưa có hoạt động</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentApproved as $approved): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?= e($approved['HoTen']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= e($approved['TenLoaiPhep']) ?></small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= formatDate($approved['NgayDuyetCap1'], 'd/m/Y H:i') ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $approved['TrangThaiCap1'] === 'Dong_y' ? 'success' : 'danger' ?>">
                                        <?= $approved['TrangThaiCap1'] === 'Dong_y' ? 'Duyệt' : 'Từ chối' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Thống kê tháng này -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Thống kê tháng <?= date('m/Y') ?>
                </h6>
                
                <?php
                $stmtMonth = $pdo->prepare("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN d.TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN d.TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
                    FROM DonNghiPhep d
                    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
                    WHERE n.MaKhoaPhong = ?
                    AND MONTH(d.NgayTao) = MONTH(CURDATE())
                    AND YEAR(d.NgayTao) = YEAR(CURDATE())
                ");
                $stmtMonth->execute([$khoaPhongId]);
                $monthStats = $stmtMonth->fetch();
                ?>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng đơn:</span>
                        <strong><?= $monthStats['total'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">Đã duyệt:</span>
                        <strong><?= $monthStats['approved'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-danger">Từ chối:</span>
                        <strong><?= $monthStats['rejected'] ?></strong>
                    </div>
                </div>
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
                        <a href="duyet_don.php" class="btn btn-warning w-100 py-3 text-white">
                            <i class="fas fa-check-double fa-2x mb-2"></i>
                            <br>Duyệt đơn
                            <?php if ($pendingCount > 0): ?>
                                <span class="badge bg-white text-warning ms-2"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="tao_don_nv.php" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <br>Tạo đơn cho NV
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="thong_ke.php" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <br>Thống kê báo cáo
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="cham_cong.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <br>Chấm công
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>