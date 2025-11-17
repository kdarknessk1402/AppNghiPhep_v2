<?php
// views/pho_hieu_truong/dashboard.php - Dashboard Phó Hiệu trưởng

$pageTitle = 'Trang chủ - Phó Hiệu Trưởng';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['PHO_HIEU_TRUONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Thống kê đơn chờ duyệt cấp 2
$stmtPending = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM DonNghiPhep
    WHERE TrangThai = 'Cho_duyet_cap_2'
    AND CapDuyetHienTai = 2
");
$stmtPending->execute();
$pendingCount = $stmtPending->fetch()['count'];

// Thống kê tổng
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai IN ('Cho_duyet_cap_1', 'Cho_duyet_cap_2', 'Cho_duyet_cap_3') THEN 1 ELSE 0 END) as pending_all,
        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
    FROM DonNghiPhep
    WHERE YEAR(NgayTao) = YEAR(CURDATE())
");
$stmtStats->execute();
$stats = $stmtStats->fetch();

// Đơn chờ duyệt
$stmtLeaves = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email,
        k.TenKhoaPhong
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    WHERE d.TrangThai = 'Cho_duyet_cap_2'
    AND d.CapDuyetHienTai = 2
    ORDER BY d.NgayTao ASC
    LIMIT 10
");
$stmtLeaves->execute();
$pendingLeaves = $stmtLeaves->fetchAll();

// Đã duyệt gần đây
$stmtApproved = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE d.NguoiDuyetCap2 = ?
    ORDER BY d.NgayDuyetCap2 DESC
    LIMIT 5
");
$stmtApproved->execute([$userId]);
$recentApproved = $stmtApproved->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3><?= $pendingCount ?></h3>
            <p class="text-muted mb-0">Đơn chờ duyệt cấp 2</p>
            <?php if ($pendingCount > 0): ?>
                <a href="duyet_don.php" class="stretched-link"></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <h3><?= $stats['total'] ?></h3>
            <p class="text-muted mb-0">Tổng đơn (năm nay)</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <h3><?= $stats['approved'] ?></h3>
            <p class="text-muted mb-0">Đã duyệt</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <h3><?= $stats['rejected'] ?></h3>
            <p class="text-muted mb-0">Từ chối</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half me-2 text-warning"></i>
                        Đơn chờ duyệt cấp 2
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
                                    <th>Mã đơn</th>
                                    <th>Nhân viên</th>
                                    <th>Khoa/Phòng</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Số ngày</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingLeaves as $leave): ?>
                                    <tr>
                                        <td><strong class="text-primary"><?= e($leave['MaDon']) ?></strong></td>
                                        <td>
                                            <strong><?= e($leave['HoTen']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= e($leave['TenLoaiPhep']) ?></small>
                                        </td>
                                        <td><?= e($leave['TenKhoaPhong'] ?? 'N/A') ?></td>
                                        <td>
                                            <?= formatDate($leave['NgayBatDauNghi']) ?>
                                            <br>đến<br>
                                            <?= formatDate($leave['NgayKetThucNghi']) ?>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                        </td>
                                        <td>
                                            <a href="duyet_don.php?action=view&id=<?= $leave['MaDon'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
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
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= e($approved['HoTen']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= formatDate($approved['NgayDuyetCap2'], 'd/m/Y H:i') ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $approved['TrangThaiCap2'] === 'Dong_y' ? 'success' : 'danger' ?>">
                                        <?= $approved['TrangThaiCap2'] === 'Dong_y' ? 'Duyệt' : 'Từ chối' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
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
                        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
                    FROM DonNghiPhep
                    WHERE MONTH(NgayTao) = MONTH(CURDATE())
                    AND YEAR(NgayTao) = YEAR(CURDATE())
                ");
                $stmtMonth->execute();
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

<div class="row g-4 mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Thao tác nhanh
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="duyet_don.php" class="btn btn-warning w-100 py-3 text-white">
                            <i class="fas fa-check-double fa-2x mb-2"></i>
                            <br>Duyệt đơn cấp 2
                            <?php if ($pendingCount > 0): ?>
                                <span class="badge bg-white text-warning ms-2"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="thong_ke.php" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <br>Thống kê tổng quan
                        </a>
                    </div>
                    <div class="col-md-4">
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