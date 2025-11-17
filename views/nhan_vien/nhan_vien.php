<?php
// views/nhan_vien/nghi_bu.php - Đăng ký nghỉ bù/làm bù

$pageTitle = 'Nghỉ bù / Làm bù';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['NHAN_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Xử lý đăng ký nghỉ bù/làm bù
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    try {
        $loai = $_POST['loai_nghi_bu']; // 'Lam_truoc_nghi_sau' hoặc 'Nghi_truoc_lam_sau'
        $ngayLamBu = $_POST['ngay_lam_bu'] ?? null;
        $buoiLamBu = $_POST['buoi_lam_bu'] ?? 'Ca_ngay';
        $ngayNghiBu = $_POST['ngay_nghi_bu'] ?? null;
        $buoiNghiBu = $_POST['buoi_nghi_bu'] ?? 'Ca_ngay';
        $lyDo = trim($_POST['ly_do']);
        
        // Validate
        if (empty($lyDo)) {
            throw new Exception('Vui lòng nhập lý do');
        }
        
        // Tính số ngày
        $soNgayLam = $buoiLamBu === 'Ca_ngay' ? 1 : 0.5;
        $soNgayNghi = $buoiNghiBu === 'Ca_ngay' ? 1 : 0.5;
        
        // Tính hạn hoàn thành (1 tháng)
        $hanHoanThanh = date('Y-m-d', strtotime('+1 month'));
        
        // Tạo mã đơn
        $maDon = generateCompensationCode();
        
        $stmt = $pdo->prepare("
            INSERT INTO NghiBuLamBu (
                MaDon, MaNguoiDung, LoaiNghiBu,
                NgayLamBu, BuoiLamBu, SoNgayLamBu,
                NgayNghiBu, BuoiNghiBu, SoNgayNghiBu,
                LyDo, TrangThai, HanHoanThanh
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Cho_xac_nhan', ?)
        ");
        
        $stmt->execute([
            $maDon,
            $userId,
            $loai,
            $ngayLamBu,
            $buoiLamBu,
            $soNgayLam,
            $ngayNghiBu,
            $buoiNghiBu,
            $soNgayNghi,
            $lyDo,
            $hanHoanThanh
        ]);
        
        logActivity('REGISTER_COMPENSATION', "Đăng ký nghỉ bù: $maDon");
        $_SESSION['success'] = 'Đăng ký nghỉ bù/làm bù thành công. Chờ xác nhận từ quản lý.';
        
        header('Location: nghi_bu.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Thống kê nghỉ bù
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN TrangThai = 'Cho_xac_nhan' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN TrangThai = 'Da_xac_nhan' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN TrangThai = 'Qua_han' THEN 1 ELSE 0 END) as expired,
        SUM(SoNgayNghiBu) as total_days_compensated
    FROM NghiBuLamBu
    WHERE MaNguoiDung = ?
");
$stmtStats->execute([$userId]);
$stats = $stmtStats->fetch();

// Danh sách nghỉ bù
$stmt = $pdo->prepare("
    SELECT *
    FROM NghiBuLamBu
    WHERE MaNguoiDung = ?
    ORDER BY NgayTao DESC
");
$stmt->execute([$userId]);
$compensations = $stmt->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="icon"><i class="fas fa-list"></i></div>
            <h3><?= $stats['total'] ?></h3>
            <p class="text-muted mb-0">Tổng đơn</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3><?= $stats['pending'] ?></h3>
            <p class="text-muted mb-0">Chờ xác nhận</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="icon"><i class="fas fa-check"></i></div>
            <h3><?= $stats['confirmed'] ?></h3>
            <p class="text-muted mb-0">Đã xác nhận</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3><?= $stats['expired'] ?></h3>
            <p class="text-muted mb-0">Quá hạn</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Form đăng ký -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Đăng ký nghỉ bù / làm bù
                </h5>
            </div>
            
            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle me-2"></i>Lưu ý:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Phải hoàn thành trong vòng 1 tháng</li>
                        <li>Số ngày nghỉ = Số ngày làm bù</li>
                    </ul>
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Loại <span class="text-danger">*</span></label>
                        <select class="form-select" name="loai_nghi_bu" id="loaiNghiBu" 
                                onchange="toggleFields()" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="Lam_truoc_nghi_sau">Làm trước - Nghỉ sau</option>
                            <option value="Nghi_truoc_lam_sau">Nghỉ trước - Làm sau</option>
                        </select>
                    </div>
                    
                    <!-- Ngày làm bù -->
                    <div id="lamBuFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Ngày làm bù <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ngay_lam_bu" id="ngayLamBu">
                            <small class="text-muted">Ngày làm thêm (T7, CN)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buổi làm bù</label>
                            <select class="form-select" name="buoi_lam_bu">
                                <option value="Ca_ngay">Cả ngày</option>
                                <option value="Buoi_sang">Buổi sáng</option>
                                <option value="Buoi_chieu">Buổi chiều</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Ngày nghỉ bù -->
                    <div id="nghiBuFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Ngày nghỉ bù <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ngay_nghi_bu" id="ngayNghiBu">
                            <small class="text-muted">Ngày sẽ nghỉ (T2-T6)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buổi nghỉ bù</label>
                            <select class="form-select" name="buoi_nghi_bu">
                                <option value="Ca_ngay">Cả ngày</option>
                                <option value="Buoi_sang">Buổi sáng</option>
                                <option value="Buoi_chieu">Buổi chiều</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="ly_do" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Danh sách -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Danh sách nghỉ bù / làm bù
                </h5>
            </div>
            
            <div class="card-body">
                <?php if (empty($compensations)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đơn nghỉ bù nào</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Loại</th>
                                    <th>Ngày làm bù</th>
                                    <th>Ngày nghỉ bù</th>
                                    <th>Hạn</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($compensations as $comp): ?>
                                    <tr>
                                        <td>
                                            <small>
                                                <?= $comp['LoaiNghiBu'] === 'Lam_truoc_nghi_sau' 
                                                    ? '<span class="badge bg-info">Làm trước</span>' 
                                                    : '<span class="badge bg-warning">Nghỉ trước</span>' ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?= formatDate($comp['NgayLamBu']) ?>
                                            <br>
                                            <small class="text-muted">
                                                (<?= formatDayCount($comp['SoNgayLamBu']) ?> ngày)
                                            </small>
                                        </td>
                                        <td>
                                            <?= formatDate($comp['NgayNghiBu']) ?>
                                            <br>
                                            <small class="text-muted">
                                                (<?= formatDayCount($comp['SoNgayNghiBu']) ?> ngày)
                                            </small>
                                        </td>
                                        <td>
                                            <small><?= formatDate($comp['HanHoanThanh']) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadges = [
                                                'Cho_xac_nhan' => '<span class="badge bg-warning">Chờ xác nhận</span>',
                                                'Da_xac_nhan' => '<span class="badge bg-success">Đã xác nhận</span>',
                                                'Qua_han' => '<span class="badge bg-danger">Quá hạn</span>',
                                                'Huy' => '<span class="badge bg-secondary">Đã hủy</span>'
                                            ];
                                            echo $statusBadges[$comp['TrangThai']] ?? '';
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
    </div>
</div>

<script>
function toggleFields() {
    const loai = document.getElementById('loaiNghiBu').value;
    const lamBuFields = document.getElementById('lamBuFields');
    const nghiBuFields = document.getElementById('nghiBuFields');
    const ngayLamBu = document.getElementById('ngayLamBu');
    const ngayNghiBu = document.getElementById('ngayNghiBu');
    
    if (loai === 'Lam_truoc_nghi_sau') {
        lamBuFields.style.display = 'block';
        nghiBuFields.style.display = 'block';
        ngayLamBu.required = true;
        ngayNghiBu.required = true;
    } else if (loai === 'Nghi_truoc_lam_sau') {
        lamBuFields.style.display = 'block';
        nghiBuFields.style.display = 'block';
        ngayLamBu.required = true;
        ngayNghiBu.required = true;
    } else {
        lamBuFields.style.display = 'none';
        nghiBuFields.style.display = 'none';
        ngayLamBu.required = false;
        ngayNghiBu.required = false;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>