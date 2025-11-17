<?php
// views/auth/create_password.php - Trang kích hoạt tài khoản

require_once __DIR__ . '/../../controllers/AuthController.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Link kích hoạt không hợp lệ';
}

// Xử lý tạo mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $matKhau = $_POST['mat_khau'] ?? '';
    $matKhauXacNhan = $_POST['mat_khau_xac_nhan'] ?? '';
    
    $authController = new AuthController();
    $result = $authController->activateAccount($token, $matKhau, $matKhauXacNhan);
    
    if ($result['success']) {
        header('Location: login.php?activated=1');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kích hoạt tài khoản - Hệ thống Nghỉ Phép</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .activation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 20px 20px 0 0;
        }
        .card-body {
            padding: 40px;
        }
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s;
        }
        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #28a745; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="activation-card mx-auto">
            <div class="card-header">
                <i class="fas fa-key fa-3x mb-3"></i>
                <h3>Kích Hoạt Tài Khoản</h3>
                <p class="mb-0">Tạo mật khẩu để hoàn tất kích hoạt</p>
            </div>
            
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($token) && empty($error)): ?>
                    <form method="POST" action="" id="activationForm">
                        <div class="mb-3">
                            <label for="mat_khau" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="mat_khau" 
                                   name="mat_khau" 
                                   placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                                   required 
                                   minlength="6">
                            <div class="password-strength" id="passwordStrength"></div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mật khẩu nên có ít nhất 6 ký tự
                            </small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="mat_khau_xac_nhan" class="form-label">
                                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="mat_khau_xac_nhan" 
                                   name="mat_khau_xac_nhan" 
                                   placeholder="Nhập lại mật khẩu"
                                   required>
                            <div class="invalid-feedback" id="passwordMismatch">
                                Mật khẩu không khớp
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-2"></i>Kích Hoạt Tài Khoản
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <i class="fas fa-times-circle text-danger fa-3x mb-3"></i>
                        <p>Link kích hoạt không hợp lệ hoặc đã hết hạn.</p>
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kiểm tra độ mạnh mật khẩu
        document.getElementById('mat_khau').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Kiểm tra mật khẩu khớp
        document.getElementById('activationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('mat_khau').value;
            const confirmPassword = document.getElementById('mat_khau_xac_nhan').value;
            const mismatchMsg = document.getElementById('passwordMismatch');
            
            if (password !== confirmPassword) {
                e.preventDefault();
                document.getElementById('mat_khau_xac_nhan').classList.add('is-invalid');
                mismatchMsg.style.display = 'block';
            }
        });
        
        document.getElementById('mat_khau_xac_nhan').addEventListener('input', function() {
            this.classList.remove('is-invalid');
            document.getElementById('passwordMismatch').style.display = 'none';
        });
    </script>
</body>
</html>