<?php
// views/pho_hieu_truong/duyet_don.php - Duyệt đơn cấp 2

$pageTitle = 'Duyệt đơn nghỉ phép - Cấp 2';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mail_config.php';

checkRole(['PHO_HIEU_TRUONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Xử lý duyệt/từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $maDon = $_POST['ma_don'];
        $action = $_POST['action'];
        $ghiChu = trim($_POST['ghi_chu'] ?? '');
        
        // Kiểm tra đơn
        $stmtCheck = $pdo->prepare("
            SELECT d.*, n.HoTen, n.Email
            FROM DonNghiPhep d
            JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
            WHERE d.MaDon = ? 
            AND d.TrangThai = 'Cho_duyet_cap_2'
            AND d.CapDuyetHienTai = 2
        ");
        $stmtCheck->execute([$maDon]);
        $don = $stmtCheck->fetch();
        
        if (!$don) {
            throw new Exception('Không tìm thấy đơn hoặc đơn đã được xử lý');
        }
        
        $pdo->beginTransaction();
        
        if ($action === 'approve') {
            // Duyệt => chuyển lên cấp 3 (Hiệu trưởng)
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Cho_duyet_cap_3',
                    CapDuyetHienTai = 3,
                    NguoiDuyetCap2 = ?,
                    NgayDuyetCap2 = NOW(),
                    TrangThaiCap2 = 'Dong_y',
                    GhiChuCap2 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            // Tìm Hiệu trưởng để gửi email
            $stmtHT = $pdo->prepare("
                SELECT Email, HoTen 
                FROM NguoiDung n
                JOIN VaiTro v ON n.MaVaiTro = v.MaVaiTro
                WHERE v.TenVaiTro IN ('HIEU_TRUONG', 'ADMIN')
                AND n.TrangThai = 'Hoat_dong'
                LIMIT 1
            ");
            $stmtHT->execute();
            $ht = $stmtHT->fetch();
            
            if ($ht) {
                sendLeaveNotificationLevel3($maDon, $ht['Email']);
            }
            
            logActivity('APPROVE_LEAVE_LEVEL2', "Duyệt đơn cấp 2: $maDon");
            $_SESSION['success'] = 'Đã duyệt đơn thành công. Đơn được chuyển lên Hiệu trưởng phê duyệt cuối.';
            
        } else {
            // Từ chối
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Tu_choi',
                    NguoiDuyetCap2 = ?,
                    NgayDuyetCap2 = NOW(),
                    TrangThaiCap2 = 'Tu_choi',
                    GhiChuCap2 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            sendLeaveResultNotification($maDon, 'Tu_choi');
            
            logActivity('REJECT_LEAVE_LEVEL2', "Từ chối đơn cấp 2: $maDon - Lý do: $ghiChu");
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

// Xem chi tiết
$viewingLeave = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $maDon = $_GET['id'];
    $stmtView = $pdo->prepare("
        SELECT 
            d.*,
            n.HoTen, n.Email, n.GioiTinh,
            k.TenKhoaPhong,
            u1.HoTen as NguoiDuyetCap1Ten
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        LEFT JOIN NguoiDung u1 ON d.NguoiDuyetCap1 = u1.MaNguoiDung
        WHERE d.MaDon = ?
    ");
    $stmtView->execute([$maDon]);
    $viewingLeave = $stmtView->fetch();
}

// Lấy danh sách đơn
$filter = $_GET['filter'] ?? 'pending';

$whereClause = "1=1";
$params = [];

if ($filter === 'pending') {
    $whereClause .= " AND d.TrangThai = 'Cho_duyet_cap_2' AND d.CapDuyetHienTai = 2";
} elseif ($filter === 'approved') {
    $whereClause .= " AND d.NguoiDuyetCap2 = ? AND d.TrangThaiCap2 = 'Dong_y'";
    $params[] = $userId;
} elseif ($filter === 'rejected') {
    $whereClause .= " AND d.NguoiDuyetCap2 = ? AND d.TrangThaiCap2 = 'Tu_choi'";
    $params[] = $userId;
}

$stmt = $pdo->prepare("
    SELECT 
        d.*,
        n.HoTen, n.Email,
        k.TenKhoaPhong
    FROM DonNghiPhep d
    JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
    LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
    WHERE $whereClause
    ORDER BY d.NgayTao DESC
");
$stmt->execute($params);
$leaves = $stmt->fetchAll();
?>

<!-- Modal tương tự như Trưởng phòng -->
<?php if ($viewingLeave): ?>
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    Chi tiết đơn - Duyệt cấp 2
                </h5>
                <a href="duyet_don.php" class="btn-close btn-close-white"></a>
            </div>
            
            <div class="modal-body">
                <!-- Thông tin cơ bản -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Mã đơn:</strong> <?= e($viewingLeave['MaDon']) ?></p>
                            <p class="mb-1"><strong>Nhân viên:</strong> <?= e($viewingLeave['HoTen']) ?></p>
                            <p class="mb-0"><strong>Khoa/Phòng:</strong> <?= e($viewingLeave['TenKhoaPhong']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Loại phép:</strong> <?= e($viewingLeave['TenLoaiPhep']) ?></p>
                            <p class="mb-0">
                                <strong>Số ngày:</strong> 
                                <span class="text-primary fs-5"><?= formatDayCount($viewingLeave['SoNgayNghi']) ?></span> ngày
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Thời gian nghỉ -->
                <div class="mb-3">
                    <p><strong>Từ:</strong> <?= formatDate($viewingLeave['NgayBatDauNghi']) ?></p>
                    <p><strong>Đến:</strong> <?= formatDate($viewingLeave['NgayKetThucNghi']) ?></p>
                    <p><strong>Lý do:</strong></p>
                    <div class="alert alert-light">
                        <?= nl2br(e($viewingLeave['LyDo'])) ?>
                    </div>
                </div>
                
                <!-- Duyệt cấp 1 -->
                <div class="alert alert-success">
                    <h6><i class="fas fa-check me-2"></i>Đã duyệt cấp 1</h6>
                    <p class="mb-1"><strong>Người duyệt:</strong> <?= e($viewingLeave['NguoiDuyetCap1Ten']) ?></p>
                    <p class="mb-1"><strong>Ngày duyệt:</strong> <?= formatDate($viewingLeave['NgayDuyetCap1'], 'd/m/Y H:i') ?></p>
                    <?php if ($viewingLeave['GhiChuCap1']): ?>
                        <p class="mb-0"><strong>Ghi chú:</strong> <?= e($viewingLeave['GhiChuCap1']) ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Form duyệt cấp 2 -->
                <?php if ($viewingLeave['TrangThai'] === 'Cho_duyet_cap_2'): ?>
                    <form method="POST">
                        <input type="hidden" name="ma_don" value="<?= e($viewingLeave['MaDon']) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi chú của bạn</label>
                            <textarea class="form-control" name="ghi_chu" rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="approve" 
                                    class="btn btn-success flex-fill"
                                    onclick="return confirm('Xác nhận DUYỆT đơn này?')">
                                <i class="fas fa-check me-2"></i>Duyệt đơn
                            </button>
                            <button type="submit" name="action" value="reject" 
                                    class="btn btn-danger flex-fill"
                                    onclick="return confirm('Xác nhận TỪ CHỐI đơn này?')">
                                <i class="fas fa-times me-2"></i>Từ chối
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="modal-footer">
                <a href="duyet_don.php" class="btn btn-secondary">Đóng</a>
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
            Duyệt đơn cấp 2 (Phó Hiệu trưởng)
        </h5>
    </div>
    
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'pending' ? 'active' : '' ?>" href="?filter=pending">
                    <i class="fas fa-clock me-2"></i>Chờ duyệt
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'approved' ? 'active' : '' ?>" href="?filter=approved">
                    <i class="fas fa-check-circle me-2"></i>Đã duyệt
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'rejected' ? 'active' : '' ?>" href="?filter=rejected">
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
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Nhân viên</th>
                            <th>Khoa/Phòng</th>
                            <th>Loại phép</th>
                            <th>Số ngày</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><strong class="text-primary"><?= e($leave['MaDon']) ?></strong></td>
                                <td><?= e($leave['HoTen']) ?></td>
                                <td><?= e($leave['TenKhoaPhong']) ?></td>
                                <td><?= e($leave['TenLoaiPhep']) ?></td>
                                <td class="text-center">
                                    <strong><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                </td>
                                <td><small><?= formatDate($leave['NgayTao'], 'd/m/Y') ?></small></td>
                                <td>
                                    <a href="?action=view&id=<?= $leave['MaDon'] ?>&filter=<?= $filter ?>" 
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>