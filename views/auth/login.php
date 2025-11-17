<?php
// views/auth/login.php - Trang đăng nhập

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../includes/session.php';

// Nếu đã đăng nhập, redirect về dashboard
if (isLoggedIn()) {
    redirectByRole();
}

$error = '';
$success = '';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = trim($_POST['ten_dang_nhap'] ?? '');
    $matKhau = $_POST['mat_khau'] ?? '';
    
    if (empty($tenDangNhap) || empty($matKhau)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $authController = new AuthController();
        $result = $authController->login($tenDangNhap, $matKhau);
        
        if ($result['success']) {
            redirectByRole();
        } else {
            $error = $result['message'];
        }
    }
}

// Hiển thị thông báo từ URL
if (isset($_GET['activated'])) {
    $success = 'Kích hoạt tài khoản thành công. Vui lòng đăng nhập.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Nghỉ Phép</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .input-group-text {
            background: transparent;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-shield"></i>
                <h2>Hệ Thống Nghỉ Phép</h2>
                <p class="mb-0">Đăng nhập để tiếp tục</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="ten_dang_nhap" class="form-label">
                            <i class="fas fa-user me-2"></i>Tên đăng nhập
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="ten_dang_nhap" 
                                   name="ten_dang_nhap" 
                                   placeholder="Nhập tên đăng nhập"
                                   required 
                                   autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="mat_khau" class="form-label">
                            <i class="fas fa-lock me-2"></i>Mật khẩu
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="mat_khau" 
                                   name="mat_khau" 
                                   placeholder="Nhập mật khẩu"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Quên mật khẩu? Liên hệ quản trị viên
                    </small>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Kích hoạt tài khoản, bấm vào <a href="create_password.php">đây</a>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-white">
                © 2025 Hệ thống Quản lý Nghỉ Phép. All rights reserved.
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>