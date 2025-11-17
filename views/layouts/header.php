<?php
// views/layouts/header.php - Header chung cho tất cả dashboard
if (!defined('HEADER_LOADED')) {
    define('HEADER_LOADED', true);
    
    require_once __DIR__ . '/../../includes/session.php';
    require_once __DIR__ . '/../../includes/functions.php';
    
    checkRole(); // Kiểm tra đăng nhập
    
    $currentUser = getCurrentUser();
}
?>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu .menu-item {
            padding: 12px 25px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu .menu-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        
        .sidebar-menu .menu-item.active {
            background: rgba(255,255,255,0.2);
            border-left-color: white;
        }
        
        .sidebar-menu .menu-item i {
            width: 25px;
            margin-right: 10px;
        }
        
        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            min-height: 100vh;
        }
        
        /* Top navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-card.primary .icon {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
        }
        
        .stat-card.success .icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .stat-card.warning .icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .stat-card.danger .icon {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block !important;
            }
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-calendar-check fa-2x mb-2"></i>
            <h4>Nghỉ Phép</h4>
            <small><?= e($currentUser['vai_tro']) ?></small>
        </div>
        
        <div class="sidebar-menu">
            <?php
            // Menu theo vai trò
            $role = $currentUser['vai_tro'];
            $basePath = '/appnghiphep_v2/views/' . strtolower(str_replace('_', '_', $role));
            
            // Menu chung
            echo '<a href="' . $basePath . '/dashboard.php" class="menu-item ' . (basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '') . '">
                    <i class="fas fa-home"></i>
                    <span>Trang chủ</span>
                  </a>';
            
            // Menu theo vai trò cụ thể
            if (in_array($role, ['NHAN_VIEN', 'GIANG_VIEN'])) {
                echo '<a href="' . $basePath . '/tao_don.php" class="menu-item">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tạo đơn nghỉ phép</span>
                      </a>';
                echo '<a href="' . $basePath . '/don_cua_toi.php" class="menu-item">
                        <i class="fas fa-file-alt"></i>
                        <span>Đơn của tôi</span>
                      </a>';
                if ($role == 'NHAN_VIEN') {
                    echo '<a href="' . $basePath . '/nghi_bu.php" class="menu-item">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Nghỉ bù/Làm bù</span>
                          </a>';
                }
            }
            
            if (in_array($role, ['TRUONG_PHONG', 'PHO_HIEU_TRUONG', 'HIEU_TRUONG', 'ADMIN'])) {
                echo '<a href="' . $basePath . '/duyet_don.php" class="menu-item">
                        <i class="fas fa-check-double"></i>
                        <span>Duyệt đơn</span>
                      </a>';
                echo '<a href="' . $basePath . '/thong_ke.php" class="menu-item">
                        <i class="fas fa-chart-bar"></i>
                        <span>Thống kê</span>
                      </a>';
            }
            
            if ($role == 'TRUONG_PHONG') {
                echo '<a href="' . $basePath . '/tao_don_nv.php" class="menu-item">
                        <i class="fas fa-user-plus"></i>
                        <span>Tạo đơn cho NV</span>
                      </a>';
                echo '<a href="' . $basePath . '/cham_cong.php" class="menu-item">
                        <i class="fas fa-clock"></i>
                        <span>Chấm công</span>
                      </a>';
            }
            
            if ($role == 'ADMIN') {
                echo '<a href="' . $basePath . '/nguoi_dung.php" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Quản lý người dùng</span>
                      </a>';
                echo '<a href="' . $basePath . '/cau_hinh.php" class="menu-item">
                        <i class="fas fa-cog"></i>
                        <span>Cấu hình hệ thống</span>
                      </a>';
            }
            ?>
            
            <a href="/appnghiphep_v2/views/profile.php" class="menu-item">
                <i class="fas fa-user-circle"></i>
                <span>Thông tin cá nhân</span>
            </a>
            
            <a href="/appnghiphep_v2/views/auth/logout.php" class="menu-item" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 d-inline-block"><?= $pageTitle ?? 'Dashboard' ?></h5>
            </div>
            
            <div class="user-info">
                <div>
                    <strong><?= e($currentUser['ho_ten']) ?></strong>
                    <br>
                    <small class="text-muted"><?= e($currentUser['email']) ?></small>
                </div>
                <div class="user-avatar">
                    <?= strtoupper(mb_substr($currentUser['ho_ten'], 0, 1)) ?>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>