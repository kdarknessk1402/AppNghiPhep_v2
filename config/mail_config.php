<?php
// config/mail_config.php - Cấu hình gửi email tự động

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

/**
 * Lấy cấu hình email từ database
 */
function getMailConfig() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM CauHinhEmail LIMIT 1");
    return $stmt->fetch();
}

/**
 * Gửi email tự động
 * 
 * @param string|array $to - Email người nhận
 * @param string $subject - Tiêu đề email
 * @param string $body - Nội dung email (HTML)
 * @param bool $isHTML - Có phải HTML không
 * @param bool $debug - Bật debug mode
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $isHTML = true, $debug = false) {
    $config = getMailConfig();
    
    if (!$config) {
        return ['success' => false, 'message' => 'Chưa cấu hình email trong hệ thống'];
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // Bật debug nếu cần
        if ($debug) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = 'html';
        }
        
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = $config['SmtpHost'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SmtpUsername'];
        $mail->Password = $config['SmtpPassword'];
        
        // Tự động phát hiện cấu hình theo loại email
        $host = strtolower($config['SmtpHost']);
        
        if (strpos($host, 'gmail') !== false) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        } elseif (strpos($host, 'office365') !== false || strpos($host, 'outlook') !== false) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        } else {
            // Email trường học hoặc server tùy chỉnh
            $mail->Port = $config['SmtpPort'];
            
            if ($config['SmtpPort'] == 587 || $config['SmtpPort'] == 25) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($config['SmtpPort'] == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }
            
            // Tắt verify SSL cho email .edu
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        }
        
        $mail->Timeout = 30;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Người gửi
        $mail->setFrom($config['EmailNguoiGui'], $config['TenNguoiGui']);
        
        // Người nhận
        if (is_array($to)) {
            foreach ($to as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($to);
        }
        
        // Nội dung
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if (!$isHTML) {
            $mail->AltBody = $body;
        }
        
        // Gửi
        $mail->send();
        
        // Lưu lịch sử gửi email
        logEmailSent($to, $subject, 'Thanh_cong');
        
        return ['success' => true, 'message' => 'Email đã được gửi thành công'];
        
    } catch (Exception $e) {
        // Lưu lỗi
        logEmailSent($to, $subject, 'That_bai', $mail->ErrorInfo);
        
        return ['success' => false, 'message' => "Lỗi gửi email: {$mail->ErrorInfo}"];
    }
}

/**
 * Gửi email kích hoạt tài khoản
 */
function sendActivationEmail($email, $hoTen, $token) {
    $activationLink = "http://" . $_SERVER['HTTP_HOST'] . "/appnghiphep_v2/views/auth/create_password.php?token=" . $token;
    
    $subject = "Kích hoạt tài khoản - Hệ thống Nghỉ Phép";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;'>
            <h1 style='margin: 0;'>🔐 Kích Hoạt Tài Khoản</h1>
        </div>
        
        <div style='padding: 30px;'>
            <p>Xin chào <strong style='color: #667eea;'>{$hoTen}</strong>,</p>
            
            <p>Tài khoản của bạn đã được tạo trong Hệ thống Quản lý Nghỉ Phép.</p>
            
            <p>Để kích hoạt tài khoản và tạo mật khẩu đăng nhập, vui lòng nhấn vào nút bên dưới:</p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$activationLink}' 
                   style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          color: white; 
                          padding: 15px 40px; 
                          text-decoration: none; 
                          border-radius: 5px; 
                          display: inline-block;
                          font-weight: bold;'>
                    Kích Hoạt Tài Khoản
                </a>
            </div>
            
            <p style='color: #dc3545;'><strong>⚠️ Lưu ý quan trọng:</strong></p>
            <ul style='color: #666;'>
                <li>Link kích hoạt có hiệu lực trong <strong>24 giờ</strong></li>
                <li>Nếu link không hoạt động, vui lòng copy và paste vào trình duyệt</li>
                <li>Sau khi kích hoạt, bạn có thể đăng nhập vào hệ thống</li>
            </ul>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                <small style='color: #666;'>
                    <strong>Link kích hoạt:</strong><br>
                    <a href='{$activationLink}' style='color: #667eea; word-break: break-all;'>{$activationLink}</a>
                </small>
            </div>
        </div>
        
        <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;'>
            <p style='margin: 0; color: #666; font-size: 12px;'>
                Email này được gửi tự động từ Hệ thống Quản lý Nghỉ Phép<br>
                Vui lòng không trả lời email này
            </p>
        </div>
    </div>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Gửi email thông báo đơn nghỉ phép (Cấp 1)
 */
function sendLeaveNotificationLevel1($maDon, $emailTruongPhong) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT d.*, n.HoTen, n.Email, n.ViTri, k.TenKhoaPhong
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        WHERE d.MaDon = ?
    ");
    $stmt->execute([$maDon]);
    $don = $stmt->fetch();
    
    if (!$don) return ['success' => false, 'message' => 'Không tìm thấy đơn'];
    
    $subject = "[CẦN DUYỆT CẤP 1] Đơn nghỉ phép - " . $don['HoTen'];
    
    $body = buildEmailTemplate([
        'title' => '📋 Đơn Nghỉ Phép Cần Duyệt',
        'level' => 'Cấp 1 - Trưởng Khoa/Phòng',
        'don' => $don,
        'action_url' => "http://" . $_SERVER['HTTP_HOST'] . "/appnghiphep_v2/views/truong_phong/dashboard.php"
    ]);
    
    return sendEmail($emailTruongPhong, $subject, $body);
}

/**
 * Gửi email thông báo đơn nghỉ phép (Cấp 2)
 */
function sendLeaveNotificationLevel2($maDon, $emailPhoHieuTruong) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT d.*, n.HoTen, n.Email, n.ViTri, k.TenKhoaPhong
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        WHERE d.MaDon = ?
    ");
    $stmt->execute([$maDon]);
    $don = $stmt->fetch();
    
    if (!$don) return ['success' => false, 'message' => 'Không tìm thấy đơn'];
    
    $subject = "[CẦN DUYỆT CẤP 2] Đơn nghỉ phép - " . $don['HoTen'];
    
    $body = buildEmailTemplate([
        'title' => '📋 Đơn Nghỉ Phép Cần Duyệt',
        'level' => 'Cấp 2 - Phó Hiệu Trưởng',
        'don' => $don,
        'action_url' => "http://" . $_SERVER['HTTP_HOST'] . "/appnghiphep_v2/views/pho_hieu_truong/dashboard.php"
    ]);
    
    return sendEmail($emailPhoHieuTruong, $subject, $body);
}

/**
 * Gửi email thông báo đơn nghỉ phép (Cấp 3)
 */
function sendLeaveNotificationLevel3($maDon, $emailHieuTruong) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT d.*, n.HoTen, n.Email, n.ViTri, k.TenKhoaPhong
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        LEFT JOIN KhoaPhong k ON n.MaKhoaPhong = k.MaKhoaPhong
        WHERE d.MaDon = ?
    ");
    $stmt->execute([$maDon]);
    $don = $stmt->fetch();
    
    if (!$don) return ['success' => false, 'message' => 'Không tìm thấy đơn'];
    
    $subject = "[PHÊ DUYỆT CUỐI] Đơn nghỉ phép - " . $don['HoTen'];
    
    $body = buildEmailTemplate([
        'title' => '📋 Đơn Nghỉ Phép Cần Phê Duyệt',
        'level' => 'Cấp 3 - Hiệu Trưởng (Quyết định cuối cùng)',
        'don' => $don,
        'action_url' => "http://" . $_SERVER['HTTP_HOST'] . "/appnghiphep_v2/views/admin/dashboard.php"
    ]);
    
    return sendEmail($emailHieuTruong, $subject, $body);
}

/**
 * Gửi email thông báo kết quả duyệt
 */
function sendLeaveResultNotification($maDon, $trangThai) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT d.*, n.HoTen, n.Email
        FROM DonNghiPhep d
        JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung
        WHERE d.MaDon = ?
    ");
    $stmt->execute([$maDon]);
    $don = $stmt->fetch();
    
    if (!$don) return ['success' => false, 'message' => 'Không tìm thấy đơn'];
    
    if ($trangThai == 'Da_duyet') {
        $subject = "✅ [PHÊ DUYỆT] Đơn nghỉ phép của bạn đã được duyệt";
        $color = '#28a745';
        $icon = '✅';
        $status = 'ĐÃ ĐƯỢC PHÊ DUYỆT';
    } else {
        $subject = "❌ [TỪ CHỐI] Đơn nghỉ phép của bạn bị từ chối";
        $color = '#dc3545';
        $icon = '❌';
        $status = 'BỊ TỪ CHỐI';
    }
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: {$color}; color: white; padding: 20px; text-align: center;'>
            <h2>{$icon} Thông Báo Kết Quả</h2>
        </div>
        <div style='padding: 20px; border: 1px solid #ddd;'>
            <p>Xin chào <strong>{$don['HoTen']}</strong>,</p>
            <p>Đơn nghỉ phép của bạn đã <strong style='color: {$color};'>{$status}</strong></p>
            
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Mã đơn:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['MaDon']}</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Loại phép:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['TenLoaiPhep']}</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Từ ngày:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($don['NgayBatDauNghi'])) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Đến ngày:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($don['NgayKetThucNghi'])) . "</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Số ngày:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>{$don['SoNgayNghi']}</strong> ngày</td>
                </tr>
            </table>
            
            <p style='text-align: center;'>
                <a href='http://" . $_SERVER['HTTP_HOST'] . "/appnghiphep_v2' 
                   style='background: {$color}; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Xem Chi Tiết
                </a>
            </p>
        </div>
    </div>
    ";
    
    return sendEmail($don['Email'], $subject, $body);
}

/**
 * Template email chung
 */
function buildEmailTemplate($data) {
    $don = $data['don'];
    $title = $data['title'];
    $level = $data['level'];
    $actionUrl = $data['action_url'];
    
    return "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0;'>
            <h2 style='margin: 0;'>{$title}</h2>
            <p style='margin: 5px 0 0 0; opacity: 0.9;'>{$level}</p>
        </div>
        
        <div style='padding: 20px;'>
            <p>Có đơn nghỉ phép mới cần xét duyệt:</p>
            
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd; width: 35%;'><strong>Mã đơn:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['MaDon']}</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Nhân viên:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['HoTen']}</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Email:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['Email']}</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Vị trí:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['ViTri']}</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Khoa/Phòng:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . ($don['TenKhoaPhong'] ?? 'N/A') . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Loại phép:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>{$don['TenLoaiPhep']}</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Từ ngày:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($don['NgayBatDauNghi'])) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Đến ngày:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($don['NgayKetThucNghi'])) . "</td>
                </tr>
                <tr style='background-color: #f8f9fa;'>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Số ngày nghỉ:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong style='color: #667eea; font-size: 18px;'>{$don['SoNgayNghi']}</strong> ngày</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'><strong>Lý do:</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . nl2br(htmlspecialchars($don['LyDo'])) . "</td>
                </tr>
            </table>
            
            <div style='text-align: center; margin-top: 20px;'>
                <a href='{$actionUrl}' 
                   style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          color: white; 
                          padding: 12px 30px; 
                          text-decoration: none; 
                          border-radius: 5px; 
                          display: inline-block;'>
                    🔗 Đăng Nhập Hệ Thống Để Duyệt
                </a>
            </div>
        </div>
        
        <div style='background-color: #f8f9fa; padding: 15px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; color: #6c757d;'>
            <p style='margin: 0;'>Email này được gửi tự động từ Hệ thống Quản lý Nghỉ Phép</p>
            <p style='margin: 5px 0 0 0;'>Vui lòng không trả lời email này</p>
        </div>
    </div>
    ";
}

/**
 * Lưu lịch sử gửi email
 */
function logEmailSent($emailNhan, $tieuDe, $trangThai, $loi = null) {
    try {
        $pdo = getDBConnection();
        
        $emailList = is_array($emailNhan) ? implode(', ', $emailNhan) : $emailNhan;
        
        $stmt = $pdo->prepare("
            INSERT INTO LichSuEmail (EmailNguoiNhan, TieuDe, TrangThai, ThongBaoLoi)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$emailList, $tieuDe, $trangThai, $loi]);
    } catch (Exception $e) {
        error_log("Log email error: " . $e->getMessage());
    }
}
?>