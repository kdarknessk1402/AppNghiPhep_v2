# Hệ Thống Quản Lý Nghỉ Phép v2.0

Hệ thống quản lý nghỉ phép cho trường học/công ty với workflow duyệt 3 cấp, hỗ trợ nhiều loại phép và quản lý nghỉ bù/làm bù.

## 🌟 Tính năng chính

### Quản lý người dùng

- 7 vai trò: Admin, Hiệu trưởng, Phó Hiệu trưởng, Trưởng khoa/phòng, Nhân viên, Giảng viên, Giáo vụ khoa
- Kích hoạt tài khoản qua email
- Quản lý thông tin cá nhân, đổi mật khẩu

### Quản lý nghỉ phép

- **Loại phép tính vào phép năm**: Phép năm, Phép ốm, Phép việc riêng
- **Loại phép không tính**: Thai sản, Hiếu, Hỷ, Không lương
- Nghỉ nửa ngày (sáng/chiều)
- Tự động tính số ngày nghỉ
- Phép năm trước dùng được đến hết Q1

### Workflow duyệt đơn

1. **Cấp 1**: Trưởng khoa/phòng
2. **Cấp 2**: Phó Hiệu trưởng
3. **Cấp 3**: Hiệu trưởng/Admin (Quyết định cuối)

### Nghỉ bù/Làm bù

- Làm trước - Nghỉ sau
- Nghỉ trước - Làm sau
- Hạn hoàn thành: 1 tháng
- Tự động chuyển sang phép năm nếu quá hạn

### Báo nghỉ/bù (Giảng viên)

- Báo nghỉ tiết dạy
- Đăng ký lịch dạy bù
- Theo dõi lịch giảng dạy

### Thông báo & Email

- Email tự động khi tạo đơn
- Email thông báo kết quả duyệt
- Email kích hoạt tài khoản

### Thống kê & Báo cáo

- Dashboard theo vai trò
- Biểu đồ thống kê
- Thống kê theo khoa/phòng
- Xuất báo cáo Excel

## 📋 Yêu cầu hệ thống

- **PHP**: >= 7.4
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.2
- **XAMPP/WAMP/LAMP**: Môi trường phát triển
- **Composer**: Quản lý dependencies
- **PHPMailer**: Gửi email

## 🚀 Cài đặt

### 1. Clone project

```bash
cd C:/xampp/htdocs/
git clone [repository-url] appnghiphep_v2
```

### 2. Cài đặt dependencies

```bash
cd appnghiphep_v2
composer install
```

### 3. Tạo database

- Mở phpMyAdmin: `http://localhost/phpmyadmin`
- Import file: `database/appnghiphep_v2.sql`
- Import dữ liệu test (tùy chọn): `database/test_data.sql`

### 4. Cấu hình database

Mở file `config/database.php` và cập nhật:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'appnghiphep_v2');
```

### 5. Cấu hình email

Mở file `config/mail_config.php` và cập nhật thông tin SMTP:

```php
// Ví dụ với Gmail
$config = [
    'SmtpHost' => 'smtp.gmail.com',
    'SmtpPort' => 587,
    'SmtpUsername' => 'your-email@gmail.com',
    'SmtpPassword' => 'your-app-password',
    'EmailNguoiGui' => 'noreply@school.edu.vn',
    'TenNguoiGui' => 'Hệ thống Nghỉ Phép'
];
```

**Lưu ý**: Với Gmail, cần tạo App Password tại: https://myaccount.google.com/apppasswords

### 6. Phân quyền thư mục

```bash
chmod -R 755 appnghiphep_v2/
chmod -R 777 appnghiphep_v2/logs/
chmod -R 777 appnghiphep_v2/uploads/
```

### 7. Truy cập ứng dụng

Mở trình duyệt: `http://localhost/appnghiphep_v2`

## 👤 Tài khoản mặc định

Sau khi import `test_data.sql`:

| Vai trò     | Username        | Mật khẩu      | Email                         |
| ----------- | --------------- | ------------- | ----------------------------- |
| Admin       | admin           | admin123      | admin@school.edu.vn           |
| Phó HT      | phohieutruong   | pht123        | pht@school.edu.vn             |
| Trưởng khoa | truongkhoa_cntt | truongkhoa123 | truongkhoa.cntt@school.edu.vn |
| Nhân viên   | nhanvien1       | nhanvien123   | nhanvien1@school.edu.vn       |
| Giảng viên  | giangvien1      | giangvien123  | giangvien1@school.edu.vn      |

## 📁 Cấu trúc thư mục

```
appnghiphep_v2/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/
│   ├── database.php
│   └── mail_config.php
├── controllers/
│   └── AuthController.php
├── database/
│   ├── appnghiphep_v2.sql
│   └── test_data.sql
├── includes/
│   ├── functions.php
│   └── session.php
├── logs/
│   └── activity.log
├── uploads/
├── views/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── duyet_don.php
│   │   └── nguoi_dung.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── create_password.php
│   ├── giang_vien/
│   │   ├── dashboard.php
│   │   └── bao_nghi.php
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── nhan_vien/
│   │   ├── dashboard.php
│   │   ├── tao_don.php
│   │   ├── don_cua_toi.php
│   │   ├── chi_tiet_don.php
│   │   └── nghi_bu.php
│   ├── pho_hieu_truong/
│   │   ├── dashboard.php
│   │   └── duyet_don.php
│   ├── truong_phong/
│   │   ├── dashboard.php
│   │   └── duyet_don.php
│   └── profile.php
├── vendor/
├── composer.json
├── index.php
└── README.md
```

## 🔧 Cấu hình nâng cao

### Cấu hình số ngày phép

Trong database, table `CauHinhHeThong`:

- `so_ngay_phep_mac_dinh`: 12 (ngày/năm)
- `han_hoan_thanh_nghi_bu`: 30 (ngày)
- `so_ngay_tao_don_truoc`: 3 (ngày)

### Thêm loại phép mới

```sql
-- Loại phép tính vào phép năm
INSERT INTO LoaiPhepTinhPhepNam (TenLoaiPhep, KyHieuChamCong)
VALUES ('Phép mới', 'P');

-- Loại phép không tính
INSERT INTO LoaiPhepKhongTinhPhepNam (TenLoaiPhep, SoNgayMacDinh, GioiTinh)
VALUES ('Phép đặc biệt', 0, 'Tat_ca');
```

### Thêm khoa/phòng

```sql
INSERT INTO KhoaPhong (TenKhoaPhong, LoaiDonVi, Email, TrangThai)
VALUES ('Khoa Mới', 'Khoa', 'khoa@school.edu.vn', 'Hoat_dong');
```

## 🐛 Xử lý lỗi thường gặp

### Lỗi kết nối database

```
⚠️ Không thể kết nối database
```

**Giải pháp**: Kiểm tra MySQL đã chạy chưa, thông tin trong `config/database.php` có đúng không.

### Lỗi gửi email

```
SMTP Error: Could not authenticate
```

**Giải pháp**:

1. Kiểm tra thông tin SMTP trong `config/mail_config.php`
2. Với Gmail: Bật "Less secure app access" hoặc dùng App Password
3. Kiểm tra firewall có chặn port 587/465 không

### Lỗi permission denied

```
Warning: mkdir(): Permission denied
```

**Giải pháp**: Cấp quyền write cho thư mục:

```bash
chmod -R 777 logs/
chmod -R 777 uploads/
```

### Lỗi session

```
Warning: session_start(): Failed to read session data
```

**Giải pháp**: Xóa session cũ trong `xampp/tmp/`

## 📖 Hướng dẫn sử dụng

### Đăng nhập lần đầu

1. Truy cập: `http://localhost/appnghiphep_v2`
2. Nhập username và password
3. Hệ thống tự động chuyển đến dashboard theo vai trò

### Tạo đơn nghỉ phép (Nhân viên)

1. Đăng nhập với vai trò Nhân viên
2. Click "Tạo đơn nghỉ phép"
3. Chọn loại phép, ngày nghỉ, lý do
4. Click "Gửi đơn"
5. Đơn sẽ được gửi email đến Trưởng phòng

### Duyệt đơn (Quản lý)

1. Đăng nhập với vai trò Trưởng phòng/Phó HT/Admin
2. Vào menu "Duyệt đơn"
3. Click "Xem chi tiết" trên đơn cần duyệt
4. Nhập ghi chú (nếu có)
5. Click "Duyệt đơn" hoặc "Từ chối"

### Thêm người dùng (Admin)

1. Đăng nhập với vai trò Admin
2. Vào "Quản lý người dùng"
3. Click "Thêm người dùng"
4. Điền thông tin, chọn vai trò
5. Hệ thống tự động gửi email kích hoạt

## 🔐 Bảo mật

- Mật khẩu được hash bằng `password_hash()` (bcrypt)
- Session timeout sau 2 giờ không hoạt động
- CSRF protection cho các form quan trọng
- SQL Injection prevention với PDO prepared statements
- XSS prevention với `htmlspecialchars()`
- Log tất cả hoạt động quan trọng

## 📞 Hỗ trợ

- Email: support@school.edu.vn
- Issue tracker: [GitHub Issues]
- Documentation: [Wiki]

## 📝 License

Copyright © 2025. All rights reserved.

## 🙏 Credits

- Bootstrap 5.3
- Font Awesome 6.4
- Chart.js
- DataTables
- PHPMailer
- jQuery

---

**Version**: 2.0.0  
**Last Updated**: November 2025  
**Author**: Development Team
