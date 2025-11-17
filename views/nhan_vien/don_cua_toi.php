<?php
// views/nhan_vien/don_cua_toi.php - Danh sách đơn nghỉ phép của tôi

$pageTitle = 'Đơn nghỉ phép của tôi';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['NHAN_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Lọc
$filter = $_GET['filter'] ?? 'all';
$year = $_GET['year'] ?? date('Y');

// Build query
$whereClause = "d.MaNguoiDung = ?";
$params = [$userId];

if ($filter !== 'all') {
    $whereClause .= " AND d.TrangThai = ?";
    $params[] = $filter;
}

$whereClause .= " AND YEAR(d.NgayTao) = ?";
$params[] = $year;

// Lấy danh sách đơn
$stmt = $pdo->prepare("
    SELECT 
        d.*,
        CASE 
            WHEN d.LoaiPhep = 'Tinh_phep_nam' THEN d.TenLoaiPhep
            ELSE d.TenLoaiPhep
        END as TenLoaiPhep
    FROM DonNghiPhep d
    WHERE $whereClause
    ORDER BY d.NgayTao DESC
");
$stmt->execute($params);
$leaves = $stmt->fetchAll();

// Thống kê
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai IN ('Cho_duyet_cap_1', 'Cho_duyet_cap_2', 'Cho_duyet_cap_3') THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN TrangThai = 'Da_duyet' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN TrangThai = 'Tu_choi' THEN 1 ELSE 0 END) as rejected
    FROM DonNghiPhep
    WHERE MaNguoiDung = ? AND YEAR(NgayTao) = ?
");
$stmtStats->execute([$userId, $year]);
$stats = $stmtStats->fetch();
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <h3><?= $stats['total'] ?></h3>
            <p class="text-muted mb-0">Tổng đơn</p>
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
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Danh sách đơn nghỉ phép
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="tao_don.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo đơn mới
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Lọc theo trạng thái:</label>
                <select class="form-select" onchange="filterLeaves(this.value)">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="Cho_duyet_cap_1" <?= $filter === 'Cho_duyet_cap_1' ? 'selected' : '' ?>>Chờ duyệt cấp 1</option>
                    <option value="Cho_duyet_cap_2" <?= $filter === 'Cho_duyet_cap_2' ? 'selected' : '' ?>>Chờ duyệt cấp 2</option>
                    <option value="Cho_duyet_cap_3" <?= $filter === 'Cho_duyet_cap_3' ? 'selected' : '' ?>>Chờ duyệt cấp 3</option>
                    <option value="Da_duyet" <?= $filter === 'Da_duyet' ? 'selected' : '' ?>>Đã duyệt</option>
                    <option value="Tu_choi" <?= $filter === 'Tu_choi' ? 'selected' : '' ?>>Từ chối</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Năm:</label>
                <select class="form-select" onchange="filterYear(this.value)">
                    <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <?php if (empty($leaves)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <p class="text-muted">Không có đơn nghỉ phép nào</p>
                <a href="tao_don.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo đơn mới
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="leavesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Mã đơn</th>
                            <th>Loại phép</th>
                            <th>Ngày nghỉ</th>
                            <th width="80">Số ngày</th>
                            <th>Lý do</th>
                            <th width="150">Trạng thái</th>
                            <th width="100">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td>
                                    <strong class="text-primary"><?= e($leave['MaDon']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= formatDate($leave['NgayTao'], 'd/m/Y H:i') ?></small>
                                </td>
                                <td>
                                    <?= e($leave['TenLoaiPhep']) ?>
                                    <br>
                                    <small class="badge bg-<?= $leave['LoaiPhep'] === 'Tinh_phep_nam' ? 'info' : 'secondary' ?>">
                                        <?= $leave['LoaiPhep'] === 'Tinh_phep_nam' ? 'Tính phép năm' : 'Không tính' ?>
                                    </small>
                                </td>
                                <td>
                                    <?= formatDate($leave['NgayBatDauNghi']) ?>
                                    <?php if ($leave['BuoiNghiBatDau'] !== 'Ca_ngay'): ?>
                                        <small class="text-muted">(<?= $leave['BuoiNghiBatDau'] === 'Buoi_sang' ? 'Sáng' : 'Chiều' ?>)</small>
                                    <?php endif; ?>
                                    <br>
                                    <i class="fas fa-arrow-down small text-muted"></i>
                                    <br>
                                    <?= formatDate($leave['NgayKetThucNghi']) ?>
                                    <?php if ($leave['BuoiNghiKetThuc'] !== 'Ca_ngay'): ?>
                                        <small class="text-muted">(<?= $leave['BuoiNghiKetThuc'] === 'Buoi_sang' ? 'Sáng' : 'Chiều' ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                    <br>
                                    <small class="text-muted">ngày</small>
                                </td>
                                <td>
                                    <small><?= nl2br(e(mb_substr($leave['LyDo'], 0, 50))) ?>
                                    <?= mb_strlen($leave['LyDo']) > 50 ? '...' : '' ?></small>
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

<script>
function filterLeaves(status) {
    const url = new URL(window.location.href);
    url.searchParams.set('filter', status);
    window.location.href = url.toString();
}

function filterYear(year) {
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    window.location.href = url.toString();
}

// Initialize DataTable
$(document).ready(function() {
    $('#leavesTable').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
        }
    });
});
</script>

<!-- Thêm DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>