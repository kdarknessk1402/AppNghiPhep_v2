<?php
// views/giang_vien/dashboard.php - Dashboard Giảng viên

$pageTitle = 'Trang chủ - Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['GIANG_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Thống kê báo nghỉ
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai = 'Cho_duyet' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
    FROM BaoNghiBaoBu
    WHERE MaGiangVien = ?
");
$stmtStats->execute([$userId]);
$stats = $stmtStats->fetch();

// Báo nghỉ gần đây
$stmtRecent = $pdo->prepare("
    SELECT 
        b.*,
        l.TenLop,
        m.TenMon
    FROM BaoNghiBaoBu b
    LEFT JOIN LopHoc l ON b.MaLop = l.MaLop
    LEFT JOIN MonHoc m ON b.MaMon = m.MaMon
    WHERE b.MaGiangVien = ?
    ORDER BY b.NgayTao DESC
    LIMIT 10
");
$stmtRecent->execute([$userId]);
$recentReports = $stmtRecent->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon"><i class="fas fa-calendar"></i></div>
            <h3><?= $stats['total'] ?></h3>
            <p class="text-muted mb-0">Tổng báo nghỉ</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3><?= $stats['pending'] ?></h3>
            <p class="text-muted mb-0">Chờ duyệt</p>
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

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                Báo nghỉ gần đây
            </h5>
            <a href="bao_nghi.php" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-2"></i>Báo nghỉ mới
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($recentReports)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có báo nghỉ nào</p>
                <a href="bao_nghi.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo báo nghỉ mới
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Lớp</th>
                            <th>Môn</th>
                            <th>Buổi nghỉ</th>
                            <th>Ngày nghỉ</th>
                            <th>Ngày bù</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentReports as $report): ?>
                            <tr>
                                <td><?= e($report['TenLop'] ?? 'N/A') ?></td>
                                <td><?= e($report['TenMon'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= ucfirst($report['BuoiNghi']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($report['NgayNghi']) ?></td>
                                <td>
                                    <?= $report['NgayBu'] ? formatDate($report['NgayBu']) : 'Chưa có' ?>
                                </td>
                                <td>
                                    <?php
                                    $badges = [
                                        'Cho_duyet' => '<span class="badge bg-warning">Chờ duyệt</span>',
                                        'Da_duyet' => '<span class="badge bg-success">Đã duyệt</span>',
                                        'Tu_choi' => '<span class="badge bg-danger">Từ chối</span>'
                                    ];
                                    echo $badges[$report['TrangThai']] ?? '';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
                    <div class="col-md-4">
                        <a href="bao_nghi.php" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <br>Báo nghỉ
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="bao_nghi.php?type=bu" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                            <br>Báo bù
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