<?php
// views/giang_vien/bao_nghi.php - Báo nghỉ và báo bù cho giảng viên

$pageTitle = 'Báo nghỉ / Báo bù';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

checkRole(['GIANG_VIEN']);

$pdo = getDBConnection();
$userId = $currentUser['ma_nguoi_dung'];

// Xử lý tạo báo nghỉ/bù
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $loai = $_POST['loai']; // 'nghi' hoặc 'bu'
        $maLop = !empty($_POST['ma_lop']) ? (int)$_POST['ma_lop'] : null;
        $maMon = !empty($_POST['ma_mon']) ? (int)$_POST['ma_mon'] : null;
        $buoi = $_POST['buoi'];
        $ngay = $_POST['ngay'];
        $ngayBu = isset($_POST['ngay_bu']) ? $_POST['ngay_bu'] : null;
        $buoiBu = isset($_POST['buoi_bu']) ? $_POST['buoi_bu'] : null;
        $lyDo = trim($_POST['ly_do']);
        
        // Validate
        if (empty($lyDo)) {
            throw new Exception('Vui lòng nhập lý do');
        }
        
        // Validate ngày nghỉ và ngày bù phải khớp nhau
        if ($loai === 'ca_hai' && (empty($ngay) || empty($ngayBu))) {
            throw new Exception('Vui lòng điền đầy đủ ngày nghỉ và ngày bù');
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO BaoNghiBaoBu (
                MaGiangVien, MaLop, MaMon,
                NgayNghi, BuoiNghi,
                NgayBu, BuoiBu,
                LyDo, TrangThai
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Cho_duyet')
        ");
        
        $stmt->execute([
            $userId,
            $maLop,
            $maMon,
            $ngay,
            $buoi,
            $ngayBu,
            $buoiBu,
            $lyDo
        ]);
        
        logActivity('CREATE_BAO_NGHI', "Tạo báo nghỉ/bù");
        $_SESSION['success'] = 'Tạo báo nghỉ/bù thành công!';
        
        header('Location: bao_nghi.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Lấy danh sách lớp học
$stmtLop = $pdo->query("
    SELECT MaLop, TenLop, MaLop as MaLopCode
    FROM LopHoc 
    WHERE TrangThai = 'Hoat_dong'
    ORDER BY TenLop
");
$lopHocs = $stmtLop->fetchAll();

// Lấy danh sách môn học
$stmtMon = $pdo->query("
    SELECT MaMon, TenMon
    FROM MonHoc
    WHERE TrangThai = 'Hoat_dong'
    ORDER BY TenMon
");
$monHocs = $stmtMon->fetchAll();

// Lấy danh sách báo nghỉ/bù của giảng viên
$stmtList = $pdo->prepare("
    SELECT 
        b.*,
        l.TenLop,
        m.TenMon
    FROM BaoNghiBaoBu b
    LEFT JOIN LopHoc l ON b.MaLop = l.MaLop
    LEFT JOIN MonHoc m ON b.MaMon = m.MaMon
    WHERE b.MaGiangVien = ?
    ORDER BY b.NgayTao DESC
");
$stmtList->execute([$userId]);
$danhSach = $stmtList->fetchAll();
?>

<div class="row g-4">
    <!-- Form tạo báo nghỉ/bù -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-times me-2"></i>
                    Tạo báo nghỉ / báo bù
                </h5>
            </div>
            
            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle me-2"></i>Lưu ý:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Số ngày nghỉ = Số ngày bù</li>
                        <li>Phải có lý do rõ ràng</li>
                    </ul>
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Loại <span class="text-danger">*</span></label>
                        <select class="form-select" name="loai" id="loaiBao" 
                                onchange="toggleBuFields()" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="nghi">Chỉ báo nghỉ</option>
                            <option value="bu">Chỉ báo bù</option>
                            <option value="ca_hai">Cả nghỉ và bù</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lớp học</label>
                        <select class="form-select" name="ma_lop">
                            <option value="">-- Không chọn --</option>
                            <?php foreach ($lopHocs as $lop): ?>
                                <option value="<?= $lop['MaLop'] ?>">
                                    <?= e($lop['TenLop']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Môn học</label>
                        <select class="form-select" name="ma_mon">
                            <option value="">-- Không chọn --</option>
                            <?php foreach ($monHocs as $mon): ?>
                                <option value="<?= $mon['MaMon'] ?>">
                                    <?= e($mon['TenMon']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Thông tin nghỉ -->
                    <div id="nghiFields">
                        <h6 class="text-primary">Thông tin nghỉ</h6>
                        <div class="mb-3">
                            <label class="form-label">Ngày nghỉ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ngay" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buổi nghỉ</label>
                            <select class="form-select" name="buoi" required>
                                <option value="Sang">Sáng</option>
                                <option value="Chieu">Chiều</option>
                                <option value="Toi">Tối</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Thông tin bù -->
                    <div id="buFields" style="display: none;">
                        <h6 class="text-success">Thông tin bù</h6>
                        <div class="mb-3">
                            <label class="form-label">Ngày bù</label>
                            <input type="date" class="form-control" name="ngay_bu">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buổi bù</label>
                            <select class="form-select" name="buoi_bu">
                                <option value="Sang">Sáng</option>
                                <option value="Chieu">Chiều</option>
                                <option value="Toi">Tối</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="ly_do" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" name="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Gửi báo cáo
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
                    Danh sách báo nghỉ / bù
                </h5>
            </div>
            
            <div class="card-body">
                <?php if (empty($danhSach)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có báo cáo nào</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Lớp/Môn</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Ngày bù</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($danhSach as $item): ?>
                                    <tr>
                                        <td>
                                            <small>
                                                <strong><?= e($item['TenLop'] ?? 'N/A') ?></strong>
                                                <br><?= e($item['TenMon'] ?? 'N/A') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= formatDate($item['NgayNghi']) ?>
                                                <br><span class="badge bg-secondary"><?= $item['BuoiNghi'] ?></span>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= $item['NgayBu'] ? formatDate($item['NgayBu']) : 'Chưa có' ?>
                                                <?php if ($item['NgayBu']): ?>
                                                    <br><span class="badge bg-success"><?= $item['BuoiBu'] ?></span>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'Cho_duyet' => '<span class="badge bg-warning">Chờ</span>',
                                                'Da_duyet' => '<span class="badge bg-success">Duyệt</span>',
                                                'Tu_choi' => '<span class="badge bg-danger">Từ chối</span>'
                                            ];
                                            echo $badges[$item['TrangThai']] ?? '';
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
function toggleBuFields() {
    const loai = document.getElementById('loaiBao').value;
    const nghiFields = document.getElementById('nghiFields');
    const buFields = document.getElementById('buFields');
    
    if (loai === 'nghi') {
        nghiFields.style.display = 'block';
        buFields.style.display = 'none';
    } else if (loai === 'bu') {
        nghiFields.style.display = 'none';
        buFields.style.display = 'block';
    } else if (loai === 'ca_hai') {
        nghiFields.style.display = 'block';
        buFields.style.display = 'block';
    } else {
        nghiFields.style.display = 'none';
        buFields.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>