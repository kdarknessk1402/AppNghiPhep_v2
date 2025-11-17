<?php
// views/truong_phong/duyet_don.php - Duyệt đơn nghỉ phép

$pageTitle = 'Duyệt đơn nghỉ phép';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mail_config.php';

checkRole(['TRUONG_PHONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];
$khoaPhongId = $currentUser['ma_khoa_phong'];

// Xử lý duyệt/từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $maDon = $_POST['ma_don'];
        $action = $_POST['action']; // 'approve' hoặc 'reject'
        $ghiChu = trim($_POST['ghi_chu'] ?? '');
        
        // Kiểm tra đơn có thuộc khoa/phòng của mình không
        $stmtCheck = $pdo->prepare("
            SELECT d.*, n.HoTen, n.Email
            FROM DonNghiPhep d
            JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
            WHERE d.MaDon = ? 
            AND n.MaKhoaPhong = ?
            AND d.TrangThai = 'Cho_duyet_cap_1'
        ");
        $stmtCheck->execute([$maDon, $khoaPhongId]);
        $don = $stmtCheck->fetch();
        
        if (!$don) {
            throw new Exception('Không tìm thấy đơn hoặc đơn đã được xử lý');
        }
        
        $pdo->beginTransaction();
        
        if ($action === 'approve') {
            // Duyệt đơn
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Cho_duyet_cap_2',
                    CapDuyetHienTai = 2,
                    NguoiDuyetCap1 = ?,
                    NgayDuyetCap1 = NOW(),
                    TrangThaiCap1 = 'Dong_y',
                    GhiChuCap1 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            // Tìm Phó Hiệu trưởng để gửi email
            $stmtPHT = $pdo->prepare("
                SELECT Email, HoTen 
                FROM NguoiDung n
                JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
                WHERE v.TenVaiTro = 'PHO_HIEU_TRUONG'
                AND n.TrangThai = 'Hoat_dong'
                LIMIT 1
            ");
            $stmtPHT->execute();
            $pht = $stmtPHT->fetch();
            
            if ($pht) {
                sendLeaveNotificationLevel2($maDon, $pht['Email']);
            }
            
            logActivity('APPROVE_LEAVE_LEVEL1', "Duyệt đơn cấp 1: $maDon");
            $_SESSION['success'] = 'Đã duyệt đơn thành công. Đơn được chuyển lên Phó Hiệu trưởng.';
            
        } else {
            // Từ chối đơn
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Tu_choi',
                    NguoiDuyetCap1 = ?,
                    NgayDuyetCap1 = NOW(),
                    TrangThaiCap1 = 'Tu_choi',
                    GhiChuCap1 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            // Gửi email thông báo từ chối
            sendLeaveResultNotification($maDon, 'Tu_choi');
            
            logActivity('REJECT_LEAVE_LEVEL1', "Từ chối đơn cấp 1: $maDon - Lý do: $ghiChu");
            $_SESSION['success'] = 'Đã từ chối đơn. Nhân viên sẽ nhận được thông báo qua email.';
        }
        
        $pdo->commit();
        header('Location: duyet_don.php');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}

// Xem chi tiết đơn
$viewingLeave = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $maDon = $_GET['id'];
    $stmtView = $pdo->prepare("
        SELECT 
            d.*,
            n.HoTen, n.Email, n.GioiTinh, n.SoDienThoai,
            k.TenKhoaPhong
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        WHERE d.MaDon = ? AND n.MaKhoaPhong = ?
    ");
    $stmtView->execute([$maDon, $khoaPhongId]);
    $viewingLeave = $stmtView->fetch();
}

// Lấy danh sách đơn chờ duyệt
$filter = $_GET['filter'] ?? 'pending';

$whereClause = "n.MaKhoaPhong = ?";
$params = [$khoaPhongId];

if ($filter === 'pending') {
    $whereClause .= " AND d.TrangThai = 'Cho_duyet_cap_1' AND d.CapDuyetHienTai = 1";
} elseif ($filter === 'approved') {
    $whereClause .= " AND d.NguoiDuyetCap1 = ? AND d.TrangThaiCap1 = 'Dong_y'";
    $params[] = $userId;
} elseif ($filter === 'rejected') {
    $whereClause .= " AND d.NguoiDuyetCap1 = ? AND d.TrangThaiCap1 = 'Tu_choi'";
    $params[] = $userId;
}

$stmt = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    WHERE $whereClause
    ORDER BY d.NgayTao DESC
");
$stmt->execute($params);
$leaves = $stmt->fetchAll();
?>

<!-- Modal chi tiết và duyệt đơn -->
<?php if ($viewingLeave): ?>
<div class="modal fade show d-block" id="viewModal" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    Chi tiết đơn nghỉ phép
                </h5>
                <a href="duyet_don.php" class="btn-close btn-close-white"></a>
            </div>
            
            <div class="modal-body">
                <!-- Thông tin nhân viên -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2 text-primary"></i>
                        Thông tin nhân viên
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Họ tên:</strong> <?= e($viewingLeave['HoTen']) ?></p>
                            <p><strong>Email:</strong> <?= e($viewingLeave['Email']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Khoa/Phòng:</strong> <?= e($viewingLeave['TenKhoaPhong']) ?></p>
                            <p><strong>Giới tính:</strong> <?= formatGender($viewingLeave['GioiTinh']) ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin đơn -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        Thông tin nghỉ phép
                    </h6>
                    
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Mã đơn:</strong> <?= e($viewingLeave['MaDon']) ?></p>
                                <p class="mb-2"><strong>Loại phép:</strong> <?= e($viewingLeave['TenLoaiPhep']) ?></p>
                                <p class="mb-0">
                                    <span class="badge bg-<?= $viewingLeave['LoaiPhep'] === 'Tinh_phep_nam' ? 'info' : 'secondary' ?>">
                                        <?= $viewingLeave['LoaiPhep'] === 'Tinh_phep_nam' ? 'Tính vào phép năm' : 'Không tính' ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Ngày tạo:</strong> <?= formatDate($viewingLeave['NgayTao'], 'd/m/Y H:i') ?></p>
                                <p class="mb-0">
                                    <strong>Số ngày nghỉ:</strong> 
                                    <span class="text-primary fs-5"><?= formatDayCount($viewingLeave['SoNgayNghi']) ?></span> ngày
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Từ ngày:</strong></p>
                            <p class="text-primary">
                                <?= formatDate($viewingLeave['NgayBatDauNghi']) ?>
                                <?php if ($viewingLeave['BuoiNghiBatDau'] !== 'Ca_ngay'): ?>
                                    <span class="badge bg-secondary">
                                        <?= $viewingLeave['BuoiNghiBatDau'] === 'Buoi_sang' ? 'Buổi sáng' : 'Buổi chiều' ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Đến ngày:</strong></p>
                            <p class="text-primary">
                                <?= formatDate($viewingLeave['NgayKetThucNghi']) ?>
                                <?php if ($viewingLeave['BuoiNghiKetThuc'] !== 'Ca_ngay'): ?>
                                    <span class="badge bg-secondary">
                                        <?= $viewingLeave['BuoiNghiKetThuc'] === 'Buoi_sang' ? 'Buổi sáng' : 'Buổi chiều' ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <p><strong>Lý do nghỉ:</strong></p>
                    <div class="alert alert-light">
                        <?= nl2br(e($viewingLeave['LyDo'])) ?>
                    </div>
                    
                    <?php if ($viewingLeave['NguoiBanGiaoCongViec']): ?>
                        <p><strong>Người được bàn giao công việc:</strong> <?= e($viewingLeave['NguoiBanGiaoCongViec']) ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Form duyệt/từ chối -->
                <?php if ($viewingLeave['TrangThai'] === 'Cho_duyet_cap_1'): ?>
                    <div class="mb-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-comment me-2 text-primary"></i>
                            Quyết định duyệt
                        </h6>
                        
                        <form method="POST" id="approvalForm">
                            <input type="hidden" name="ma_don" value="<?= e($viewingLeave['MaDon']) ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Ghi chú (tùy chọn)</label>
                                <textarea class="form-control" name="ghi_chu" rows="3" 
                                          placeholder="Nhập ghi chú nếu cần..."></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="approve" 
                                        class="btn btn-success flex-fill"
                                        onclick="return confirm('Xác nhận DUYỆT đơn này?')">
                                    <i class="fas fa-check me-2"></i>Duyệt đơn
                                </button>
                                <button type="submit" name="action" value="reject" 
                                        class="btn btn-danger flex-fill"
                                        onclick="return confirm('Xác nhận TỪ CHỐI đơn này?\nVui lòng nhập lý do từ chối ở trên.')">
                                    <i class="fas fa-times me-2"></i>Từ chối
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="modal-footer">
                <a href="duyet_don.php" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Đóng
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách đơn -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-check-double me-2 text-primary"></i>
            Duyệt đơn nghỉ phép
        </h5>
    </div>
    
    <div class="card-body">
        <!-- Filter tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'pending' ? 'active' : '' ?>" 
                   href="?filter=pending">
                    <i class="fas fa-clock me-2"></i>Chờ duyệt
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'approved' ? 'active' : '' ?>" 
                   href="?filter=approved">
                    <i class="fas fa-check-circle me-2"></i>Đã duyệt
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'rejected' ? 'active' : '' ?>" 
                   href="?filter=rejected">
                    <i class="fas fa-times-circle me-2"></i>Đã từ chối
                </a>
            </li>
        </ul>
        
        <?php if (empty($leaves)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <p class="text-muted">Không có đơn nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="leavesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Nhân viên</th>
                            <th>Loại phép</th>
                            <th>Ngày nghỉ</th>
                            <th>Số ngày</th>
                            <th>Ngày tạo</th>
                            <?php if ($filter !== 'pending'): ?>
                                <th>Trạng thái</th>
                            <?php endif; ?>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><strong class="text-primary"><?= e($leave['MaDon']) ?></strong></td>
                                <td>
                                    <strong><?= e($leave['HoTen']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= e($leave['Email']) ?></small>
                                </td>
                                <td><?= e($leave['TenLoaiPhep']) ?></td>
                                <td>
                                    <?= formatDate($leave['NgayBatDauNghi']) ?>
                                    <br>
                                    <i class="fas fa-arrow-down small text-muted"></i>
                                    <br>
                                    <?= formatDate($leave['NgayKetThucNghi']) ?>
                                </td>
                                <td class="text-center">
                                    <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                </td>
                                <td>
                                    <small><?= formatDate($leave['NgayTao'], 'd/m/Y H:i') ?></small>
                                </td>
                                <?php if ($filter !== 'pending'): ?>
                                    <td><?= getStatusBadge($leave['TrangThai']) ?></td>
                                <?php endif; ?>
                                <td>
                                    <a href="?action=view&id=<?= $leave['MaDon'] ?>&filter=<?= $filter ?>" 
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#leavesTable').DataTable({
        pageLength: 10,
        order: [[5, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>