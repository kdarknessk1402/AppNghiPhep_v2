<?php
// views/admin/dashboard.php - Dashboard Admin/Hiệu trưởng

$pageTitle = 'Trang chủ - Quản trị';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['ADMIN', 'HIEU_TRUONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Thống kê tổng quan
$stmtStats = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM NguoiDung WHERE TrangThai = 'Hoat_dong') as total_users,
        (SELECT COUNT(*) FROM DonNghiPhep WHERE TrangThai IN ('Cho_duyet_cap_1', 'Cho_duyet_cap_2', 'Cho_duyet_cap_3')) as pending_leaves,
        (SELECT COUNT(*) FROM DonNghiPhep WHERE TrangThai = 'Cho_duyet_cap_3' AND CapDuyetHienTai = 3) as pending_level3,
        (SELECT COUNT(*) FROM DonNghiPhep WHERE YEAR(NgayTao) = YEAR(CURDATE())) as total_leaves_year,
        (SELECT COUNT(*) FROM DonNghiPhep WHERE TrangThai = 'Da_duyet' AND YEAR(NgayTao) = YEAR(CURDATE())) as approved_year,
        (SELECT COUNT(*) FROM KhoaPhong WHERE TrangThai = 'Hoat_dong') as total_departments
");
$stmtStats->execute();
$stats = $stmtStats->fetch();

// Đơn chờ duyệt cấp 3 (quyết định cuối)
$stmtPendingLevel3 = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email,
        k.TenKhoaPhong,
        u1.HoTen as NguoiDuyetCap1Ten,
        u2.HoTen as NguoiDuyetCap2Ten
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    LEFT JOIN NguoiDung u1 ON d.NguoiDuyetCap1 = u1.MaNguoiDung
    LEFT JOIN NguoiDung u2 ON d.NguoiDuyetCap2 = u2.MaNguoiDung
    WHERE d.TrangThai = 'Cho_duyet_cap_3'
    AND d.CapDuyetHienTai = 3
    ORDER BY d.NgayTao ASC
    LIMIT 10
");
$stmtPendingLevel3->execute();
$pendingLevel3 = $stmtPendingLevel3->fetchAll();

// Thống kê theo khoa/phòng
$stmtDepartmentStats = $pdo->prepare("
    SELECT 
        k.TenKhoaPhong,
        COUNT(DISTINCT n.MaNguoiDung) as total_employees,
        COUNT(d.MaDon) as total_leaves,
        SUM(CASE WHEN d.TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved_leaves
    FROM KhoaPhong k
    LEFT JOIN NguoiDung n ON k.MaKhoaPhong = n.MaKhoaPhong AND n.TrangThai = 'Hoat_dong'
    LEFT JOIN DonNghiPhep d ON n.MaNguoiDung = d.MaNguoiDung AND YEAR(d.NgayTao) = YEAR(CURDATE())
    WHERE k.TrangThai = 'Hoat_dong'
    GROUP BY k.MaKhoaPhong
    ORDER BY total_leaves DESC
");
$stmtDepartmentStats->execute();
$departmentStats = $stmtDepartmentStats->fetchAll();

// Thống kê theo tháng trong năm
$stmtMonthlyStats = $pdo->prepare("
    SELECT 
        MONTH(NgayTao) as month,
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved
    FROM DonNghiPhep
    WHERE YEAR(NgayTao) = YEAR(CURDATE())
    GROUP BY MONTH(NgayTao)
    ORDER BY month
");
$stmtMonthlyStats->execute();
$monthlyStats = $stmtMonthlyStats->fetchAll();

// Người dùng mới gần đây
$stmtNewUsers = $pdo->prepare("
    SELECT n.HoTen, n.Email, v.TenVaiTro, n.NgayTao
    FROM NguoiDung n
    JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
    ORDER BY n.NgayTao DESC
    LIMIT 5
");
$stmtNewUsers->execute();
$newUsers = $stmtNewUsers->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon"><i class="fas fa-users"></i></div>
            <h3><?= $stats['total_users'] ?></h3>
            <p class="text-muted mb-0">Tổng người dùng</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3><?= $stats['pending_level3'] ?></h3>
            <p class="text-muted mb-0">Chờ phê duyệt cuối</p>
            <?php if ($stats['pending_level3'] > 0): ?>
                <a href="duyet_don.php" class="stretched-link"></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <h3><?= $stats['approved_year'] ?></h3>
            <p class="text-muted mb-0">Đã duyệt (năm nay)</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="icon"><i class="fas fa-building"></i></div>
            <h3><?= $stats['total_departments'] ?></h3>
            <p class="text-muted mb-0">Khoa/Phòng</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Đơn chờ phê duyệt cuối -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel me-2 text-warning"></i>
                        Đơn chờ phê duyệt cuối
                    </h5>
                    <?php if ($stats['pending_level3'] > 0): ?>
                        <a href="duyet_don.php" class="btn btn-sm btn-warning text-white">
                            Xem tất cả <span class="badge bg-white text-warning"><?= $stats['pending_level3'] ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($pendingLevel3)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p class="text-muted">Không có đơn nào chờ phê duyệt</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Khoa/Phòng</th>
                                    <th>Loại phép</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Số ngày</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingLevel3 as $leave): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($leave['HoTen']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= e($leave['Email']) ?></small>
                                        </td>
                                        <td><?= e($leave['TenKhoaPhong']) ?></td>
                                        <td>
                                            <?= e($leave['TenLoaiPhep']) ?>
                                            <br>
                                            <small class="text-success">
                                                <i class="fas fa-check"></i> <?= e($leave['NguoiDuyetCap1Ten']) ?>
                                            </small>
                                            <br>
                                            <small class="text-success">
                                                <i class="fas fa-check"></i> <?= e($leave['NguoiDuyetCap2Ten']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= formatDate($leave['NgayBatDauNghi']) ?>
                                                <br>đến<br>
                                                <?= formatDate($leave['NgayKetThucNghi']) ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                        </td>
                                        <td>
                                            <a href="duyet_don.php?action=view&id=<?= $leave['MaDon'] ?>" 
                                               class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-gavel"></i>
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
        
        <!-- Thống kê theo khoa/phòng -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2 text-primary"></i>
                    Thống kê theo Khoa/Phòng
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Khoa/Phòng</th>
                                <th class="text-center">Nhân viên</th>
                                <th class="text-center">Tổng đơn</th>
                                <th class="text-center">Đã duyệt</th>
                                <th class="text-center">Tỷ lệ duyệt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departmentStats as $dept): ?>
                                <tr>
                                    <td><strong><?= e($dept['TenKhoaPhong']) ?></strong></td>
                                    <td class="text-center"><?= $dept['total_employees'] ?></td>
                                    <td class="text-center"><?= $dept['total_leaves'] ?></td>
                                    <td class="text-center"><?= $dept['approved_leaves'] ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $rate = $dept['total_leaves'] > 0 
                                            ? round(($dept['approved_leaves'] / $dept['total_leaves']) * 100) 
                                            : 0;
                                        ?>
                                        <span class="badge bg-<?= $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'secondary') ?>">
                                            <?= $rate ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Biểu đồ tháng -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Đơn nghỉ phép theo tháng
                </h6>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="200"></canvas>
            </div>
        </div>
        
        <!-- Người dùng mới -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-user-plus me-2 text-primary"></i>
                    Người dùng mới gần đây
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($newUsers)): ?>
                    <p class="text-muted text-center">Chưa có người dùng mới</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($newUsers as $user): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= e($user['HoTen']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= e($user['TenVaiTro']) ?>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?= formatDate($user['NgayTao'], 'd/m/Y') ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                        <a href="duyet_don.php" class="btn btn-warning w-100 py-3 text-white">
                            <i class="fas fa-gavel fa-2x mb-2"></i>
                            <br>Phê duyệt đơn
                            <?php if ($stats['pending_level3'] > 0): ?>
                                <span class="badge bg-white text-warning ms-2"><?= $stats['pending_level3'] ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="nguoi_dung.php" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <br>Quản lý người dùng
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="thong_ke.php" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <br>Thống kê báo cáo
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="cau_hinh.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-cog fa-2x mb-2"></i>
                            <br>Cấu hình hệ thống
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ đơn theo tháng
const monthlyData = <?= json_encode($monthlyStats) ?>;
const monthLabels = monthlyData.map(m => 'T' + m.month);
const totalData = monthlyData.map(m => m.total);
const approvedData = monthlyData.map(m => m.approved);

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Tổng đơn',
            data: totalData,
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4
        }, {
            label: 'Đã duyệt',
            data: approvedData,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>