<?php
// views/admin/duyet_don.php - Phê duyệt đơn cấp 3 (Quyết định cuối)

$pageTitle = 'Phê duyệt đơn nghỉ phép';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mail_config.php';

checkRole(['ADMIN', 'HIEU_TRUONG']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Xử lý phê duyệt/từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $maDon = $_POST['ma_don'];
        $action = $_POST['action'];
        $ghiChu = trim($_POST['ghi_chu'] ?? '');
        
        // Kiểm tra đơn
        $stmtCheck = $pdo->prepare("
            SELECT d.*, n.HoTen, n.Email, n.MaNguoiDung,
                   n.SoNgayPhepDaDung, n.SoNgayPhepTonNamTruoc
            FROM DonNghiPhep d
            JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
            WHERE d.MaDon = ? 
            AND d.TrangThai = 'Cho_duyet_cap_3'
            AND d.CapDuyetHienTai = 3
        ");
        $stmtCheck->execute([$maDon]);
        $don = $stmtCheck->fetch();
        
        if (!$don) {
            throw new Exception('Không tìm thấy đơn hoặc đơn đã được xử lý');
        }
        
        $pdo->beginTransaction();
        
        if ($action === 'approve') {
            // PHÊ DUYỆT CUỐI - Đơn được chấp thuận
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Da_duyet',
                    NguoiDuyetCap3 = ?,
                    NgayDuyetCap3 = NOW(),
                    TrangThaiCap3 = 'Dong_y',
                    GhiChuCap3 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            // Cập nhật số ngày phép đã dùng nếu là loại tính phép năm
            if ($don['LoaiPhep'] === 'Tinh_phep_nam') {
                $soNgayNghi = $don['SoNgayNghi'];
                $phepDaDung = $don['SoNgayPhepDaDung'];
                $phepTonNamTruoc = $don['SoNgayPhepTonNamTruoc'];
                
                // Ưu tiên trừ phép năm trước (nếu đang trong Q1)
                $currentMonth = (int)date('n');
                if ($currentMonth >= 1 && $currentMonth <= 3 && $phepTonNamTruoc > 0) {
                    // Trừ phép năm trước trước
                    $truPhepCu = min($soNgayNghi, $phepTonNamTruoc);
                    $phepTonNamTruoc -= $truPhepCu;
                    $soNgayNghi -= $truPhepCu;
                }
                
                // Trừ phép năm hiện tại
                $phepDaDung += $soNgayNghi;
                
                $stmtUpdateLeave = $pdo->prepare("
                    UPDATE NguoiDung 
                    SET SoNgayPhepDaDung = ?,
                        SoNgayPhepTonNamTruoc = ?
                    WHERE MaNguoiDung = ?
                ");
                $stmtUpdateLeave->execute([$phepDaDung, $phepTonNamTruoc, $don['MaNguoiDung']]);
            }
            
            // Gửi email thông báo duyệt
            sendLeaveResultNotification($maDon, 'Da_duyet');
            
            logActivity('APPROVE_LEAVE_FINAL', "Phê duyệt cuối đơn: $maDon");
            $_SESSION['success'] = 'Đã phê duyệt đơn thành công. Nhân viên sẽ nhận được thông báo qua email.';
            
        } else {
            // TỪ CHỐI
            $stmtUpdate = $pdo->prepare("
                UPDATE DonNghiPhep 
                SET TrangThai = 'Tu_choi',
                    NguoiDuyetCap3 = ?,
                    NgayDuyetCap3 = NOW(),
                    TrangThaiCap3 = 'Tu_choi',
                    GhiChuCap3 = ?
                WHERE MaDon = ?
            ");
            $stmtUpdate->execute([$userId, $ghiChu, $maDon]);
            
            sendLeaveResultNotification($maDon, 'Tu_choi');
            
            logActivity('REJECT_LEAVE_FINAL', "Từ chối đơn cấp 3: $maDon - Lý do: $ghiChu");
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
            n.HoTen, n.Email, n.GioiTinh, n.SoNgayPhepNam, n.SoNgayPhepDaDung,
            k.TenKhoaPhong,
            u1.HoTen as NguoiDuyetCap1Ten,
            u2.HoTen as NguoiDuyetCap2Ten
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        LEFT JOIN NguoiDung u1 ON d.NguoiDuyetCap1 = u1.MaNguoiDung
        LEFT JOIN NguoiDung u2 ON d.NguoiDuyetCap2 = u2.MaNguoiDung
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
    $whereClause .= " AND d.TrangThai = 'Cho_duyet_cap_3' AND d.CapDuyetHienTai = 3";
} elseif ($filter === 'approved') {
    $whereClause .= " AND d.NguoiDuyetCap3 = ? AND d.TrangThaiCap3 = 'Dong_y'";
    $params[] = $userId;
} elseif ($filter === 'rejected') {
    $whereClause .= " AND d.NguoiDuyetCap3 = ? AND d.TrangThaiCap3 = 'Tu_choi'";
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

<!-- Modal chi tiết -->
<?php if ($viewingLeave): ?>
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-gavel me-2"></i>
                    Phê duyệt cuối - Quyết định của Hiệu trưởng
                </h5>
                <a href="duyet_don.php" class="btn-close btn-close-white"></a>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Cột trái - Thông tin đơn -->
                    <div class="col-md-8">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-info-circle me-2"></i>Thông tin đơn nghỉ phép</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Mã đơn:</strong> <?= e($viewingLeave['MaDon']) ?></p>
                                    <p class="mb-1"><strong>Nhân viên:</strong> <?= e($viewingLeave['HoTen']) ?></p>
                                    <p class="mb-0"><strong>Khoa/Phòng:</strong> <?= e($viewingLeave['TenKhoaPhong']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Loại phép:</strong> <?= e($viewingLeave['TenLoaiPhep']) ?></p>
                                    <p class="mb-0">
                                        <strong>Số ngày nghỉ:</strong> 
                                        <span class="fs-5 text-danger"><?= formatDayCount($viewingLeave['SoNgayNghi']) ?></span> ngày
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Thời gian nghỉ:</strong></p>
                            <p class="text-primary">
                                Từ <?= formatDate($viewingLeave['NgayBatDauNghi']) ?>
                                đến <?= formatDate($viewingLeave['NgayKetThucNghi']) ?>
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Lý do nghỉ:</strong></p>
                            <div class="alert alert-light">
                                <?= nl2br(e($viewingLeave['LyDo'])) ?>
                            </div>
                        </div>
                        
                        <!-- Tiến trình duyệt -->
                        <div class="mb-3">
                            <h6 class="border-bottom pb-2 mb-3">Tiến trình duyệt</h6>
                            
                            <div class="alert alert-success">
                                <strong><i class="fas fa-check-circle me-2"></i>Cấp 1 - Đã duyệt</strong>
                                <p class="mb-0 mt-2">
                                    <strong>Người duyệt:</strong> <?= e($viewingLeave['NguoiDuyetCap1Ten']) ?><br>
                                    <strong>Ngày:</strong> <?= formatDate($viewingLeave['NgayDuyetCap1'], 'd/m/Y H:i') ?>
                                    <?php if ($viewingLeave['GhiChuCap1']): ?>
                                        <br><strong>Ghi chú:</strong> <?= e($viewingLeave['GhiChuCap1']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="alert alert-success">
                                <strong><i class="fas fa-check-circle me-2"></i>Cấp 2 - Đã duyệt</strong>
                                <p class="mb-0 mt-2">
                                    <strong>Người duyệt:</strong> <?= e($viewingLeave['NguoiDuyetCap2Ten']) ?><br>
                                    <strong>Ngày:</strong> <?= formatDate($viewingLeave['NgayDuyetCap2'], 'd/m/Y H:i') ?>
                                    <?php if ($viewingLeave['GhiChuCap2']): ?>
                                        <br><strong>Ghi chú:</strong> <?= e($viewingLeave['GhiChuCap2']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cột phải - Form phê duyệt -->
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-user-check me-2"></i>
                                    Thông tin phép năm
                                </h6>
                                <p class="mb-2">
                                    <strong>Tổng phép năm:</strong> 
                                    <?= formatDayCount($viewingLeave['SoNgayPhepNam']) ?> ngày
                                </p>
                                <p class="mb-2">
                                    <strong>Đã sử dụng:</strong> 
                                    <?= formatDayCount($viewingLeave['SoNgayPhepDaDung']) ?> ngày
                                </p>
                                <p class="mb-0">
                                    <strong>Còn lại:</strong> 
                                    <span class="text-success">
                                        <?= formatDayCount($viewingLeave['SoNgayPhepNam'] - $viewingLeave['SoNgayPhepDaDung']) ?> ngày
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($viewingLeave['TrangThai'] === 'Cho_duyet_cap_3'): ?>
                            <div class="card mt-3 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-gavel me-2"></i>
                                        Quyết định phê duyệt
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="ma_don" value="<?= e($viewingLeave['MaDon']) ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Ghi chú của bạn</label>
                                            <textarea class="form-control" name="ghi_chu" rows="4" 
                                                      placeholder="Nhập ghi chú (tùy chọn)..."></textarea>
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Lưu ý:</strong> Đây là quyết định cuối cùng. 
                                                Sau khi phê duyệt, đơn sẽ được chấp thuận và phép năm của nhân viên sẽ được cập nhật.
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="action" value="approve" 
                                                    class="btn btn-success"
                                                    onclick="return confirm('XÁC NHẬN PHÊ DUYỆT đơn này?\n\nĐây là quyết định cuối cùng.')">
                                                <i class="fas fa-check-circle me-2"></i>Phê duyệt đơn
                                            </button>
                                            <button type="submit" name="action" value="reject" 
                                                    class="btn btn-danger"
                                                    onclick="return confirm('XÁC NHẬN TỪ CHỐI đơn này?\n\nVui lòng nhập lý do từ chối.')">
                                                <i class="fas fa-times-circle me-2"></i>Từ chối đơn
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
            <i class="fas fa-gavel me-2 text-danger"></i>
            Phê duyệt đơn nghỉ phép (Cấp 3 - Quyết định cuối)
        </h5>
    </div>
    
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'pending' ? 'active' : '' ?>" href="?filter=pending">
                    <i class="fas fa-clock me-2"></i>Chờ phê duyệt
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'approved' ? 'active' : '' ?>" href="?filter=approved">
                    <i class="fas fa-check-circle me-2"></i>Đã phê duyệt
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
                                <td><strong class="text-danger"><?= e($leave['MaDon']) ?></strong></td>
                                <td>
                                    <strong><?= e($leave['HoTen']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= e($leave['Email']) ?></small>
                                </td>
                                <td><?= e($leave['TenKhoaPhong']) ?></td>
                                <td><?= e($leave['TenLoaiPhep']) ?></td>
                                <td class="text-center">
                                    <strong class="text-primary"><?= formatDayCount($leave['SoNgayNghi']) ?></strong>
                                </td>
                                <td><small><?= formatDate($leave['NgayTao'], 'd/m/Y') ?></small></td>
                                <td>
                                    <a href="?action=view&id=<?= $leave['MaDon'] ?>&filter=<?= $filter ?>" 
                                       class="btn btn-sm btn-<?= $filter === 'pending' ? 'danger' : 'outline-primary' ?>">
                                        <i class="fas fa-<?= $filter === 'pending' ? 'gavel' : 'eye' ?>"></i>
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