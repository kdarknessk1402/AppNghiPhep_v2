<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Hệ thống Nghỉ Phép' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/appnghiphep_v2/assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 260px;
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .nav-link {
            color: #333;
            padding: 12px 20px;
            transition: all 0.3s;
            display: block;
            text-decoration: none;
        }
        
        .nav-link:hover {
            background: #f5f5f5;
            color: var(--primary-color);
            padding-left: 25px;
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background: white;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
            font-weight: bold;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: transform 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }
        
        .stats-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-info {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .badge-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        table {
            background: white;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="navbar-brand">
            <i class="fas fa-calendar-check me-2"></i>
            Hệ Thống Nghỉ Phép
        </div>
        
        <!-- User Info -->
        <?php if (isset($currentUser) && $currentUser): ?>
        <div class="user-info">
            <div class="fw-bold"><?= htmlspecialchars($currentUser['HoTen'] ?? 'User') ?></div>
            <div class="text-muted small"><?= htmlspecialchars($currentUser['Email'] ?? '') ?></div>
            <span class="badge bg-primary mt-2"><?= htmlspecialchars($currentUser['TenVaiTro'] ?? '') ?></span>
        </div>
        <?php endif; ?>
        
        <!-- Navigation Menu -->
        <nav class="nav flex-column mt-3">
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            $vaiTro = $_SESSION['vai_tro'] ?? '';
            ?>
            
            <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="/appnghiphep_v2/index.php">
                <i class="fas fa-home me-2"></i> Trang chủ
            </a>
            
            <?php if ($vaiTro == 'admin'): ?>
                <a class="nav-link <?= $currentPage == 'nguoi_dung.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/admin/nguoi_dung.php">
                    <i class="fas fa-users me-2"></i> Quản lý người dùng
                </a>
                <a class="nav-link <?= $currentPage == 'thong_ke.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/admin/thong_ke.php">
                    <i class="fas fa-chart-bar me-2"></i> Thống kê
                </a>
                <a class="nav-link <?= $currentPage == 'cau_hinh.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/admin/cau_hinh.php">
                    <i class="fas fa-cog me-2"></i> Cấu hình hệ thống
                </a>
            
            <?php elseif ($vaiTro == 'giang_vien'): ?>
                <a class="nav-link <?= $currentPage == 'tao_don.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/giang_vien/tao_don.php">
                    <i class="fas fa-plus-circle me-2"></i> Tạo đơn nghỉ - dạy bù
                </a>
                <a class="nav-link <?= $currentPage == 'don_cua_toi.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/giang_vien/don_cua_toi.php">
                    <i class="fas fa-file-alt me-2"></i> Đơn của tôi
                </a>
            
            <?php elseif ($vaiTro == 'hieu_truong'): ?>
                <a class="nav-link <?= $currentPage == 'duyet_don.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/hieu_truong/duyet_don.php">
                    <i class="fas fa-check-circle me-2"></i> Duyệt đơn
                </a>
                <a class="nav-link <?= $currentPage == 'thong_ke.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/hieu_truong/thong_ke.php">
                    <i class="fas fa-chart-line me-2"></i> Thống kê
                </a>
            
            <?php elseif ($vaiTro == 'nhan_vien'): ?>
                <a class="nav-link <?= $currentPage == 'tao_don.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/nhan_vien/tao_don.php">
                    <i class="fas fa-plus-circle me-2"></i> Tạo đơn nghỉ bù - làm bù
                </a>
                <a class="nav-link <?= $currentPage == 'don_cua_toi.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/nhan_vien/don_cua_toi.php">
                    <i class="fas fa-file-alt me-2"></i> Đơn của tôi
                </a>
            
            <?php elseif ($vaiTro == 'pho_hieu_truong'): ?>
                <a class="nav-link <?= $currentPage == 'duyet_don.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/pho_hieu_truong/duyet_don.php">
                    <i class="fas fa-check-circle me-2"></i> Duyệt đơn
                </a>
                <a class="nav-link <?= $currentPage == 'thong_ke.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/pho_hieu_truong/thong_ke.php">
                    <i class="fas fa-chart-line me-2"></i> Thống kê
                </a>
            
            <?php elseif ($vaiTro == 'truong_phong'): ?>
                <a class="nav-link <?= $currentPage == 'duyet_don.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/truong_phong/duyet_don.php">
                    <i class="fas fa-check-circle me-2"></i> Duyệt đơn
                </a>
                <a class="nav-link <?= $currentPage == 'cham_cong.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/truong_phong/cham_cong.php">
                    <i class="fas fa-clock me-2"></i> Chấm công
                </a>
                <a class="nav-link <?= $currentPage == 'tao_don_nhan_vien.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/truong_phong/tao_don_nhan_vien.php">
                    <i class="fas fa-user-plus me-2"></i> Tạo đơn cho nhân viên
                </a>
                <a class="nav-link <?= $currentPage == 'thong_ke.php' ? 'active' : '' ?>" href="/appnghiphep_v2/views/truong_phong/thong_ke.php">
                    <i class="fas fa-chart-bar me-2"></i> Thống kê
                </a>
            <?php endif; ?>
            
            <!-- Đăng xuất -->
            <a class="nav-link text-danger mt-3" href="/appnghiphep_v2/views/auth/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">