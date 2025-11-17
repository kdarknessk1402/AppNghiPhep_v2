-- database/test_data.sql - Dữ liệu test cho hệ thống

USE appnghiphep_v2;

-- Thêm Khoa/Phòng mẫu
INSERT INTO KhoaPhong (TenKhoaPhong, LoaiDonVi, Email, TrangThai) VALUES
('Khoa Công nghệ Thông tin', 'Khoa', 'cntt@school.edu.vn', 'Hoat_dong'),
('Khoa Kinh tế', 'Khoa', 'kt@school.edu.vn', 'Hoat_dong'),
('Phòng Hành chính', 'Phong', 'hc@school.edu.vn', 'Hoat_dong');

-- Lấy ID khoa vừa tạo
SET @khoa_cntt = (SELECT MaKhoaPhong FROM KhoaPhong WHERE TenKhoaPhong = 'Khoa Công nghệ Thông tin' LIMIT 1);
SET @khoa_kt = (SELECT MaKhoaPhong FROM KhoaPhong WHERE TenKhoaPhong = 'Khoa Kinh tế' LIMIT 1);
SET @phong_hc = (SELECT MaKhoaPhong FROM KhoaPhong WHERE TenKhoaPhong = 'Phòng Hành chính' LIMIT 1);

-- Tạo tài khoản Admin (mật khẩu: admin123)
INSERT INTO NguoiDung (MaNguoiDung, TenDangNhap, MatKhau, HoTen, Email, GioiTinh, MaVaiTro, NgayBatDauLamViec, TrangThai) VALUES
('ADMIN001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin@school.edu.vn', 'Nam', 1, '2020-01-01', 'Hoat_dong');

-- Tạo tài khoản Phó Hiệu trưởng (mật khẩu: pht123)
INSERT INTO NguoiDung (MaNguoiDung, TenDangNhap, MatKhau, HoTen, Email, GioiTinh, MaVaiTro, NgayBatDauLamViec, TrangThai) VALUES
('PHT001', 'phohieutruong', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Phó HT', 'pht@school.edu.vn', 'Nam', 3, '2018-01-01', 'Hoat_dong');

-- Tạo tài khoản Trưởng khoa CNTT (mật khẩu: truongkhoa123)
INSERT INTO NguoiDung (MaNguoiDung, TenDangNhap, MatKhau, HoTen, Email, GioiTinh, MaVaiTro, MaKhoaPhong, NgayBatDauLamViec, TrangThai) VALUES
('TP001', 'truongkhoa_cntt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Trưởng Khoa', 'truongkhoa.cntt@school.edu.vn', 'Nu', 4, @khoa_cntt, '2019-01-01', 'Hoat_dong');

-- Cập nhật Trưởng phòng cho khoa
UPDATE KhoaPhong SET MaTruongPhong = 'TP001' WHERE MaKhoaPhong = @khoa_cntt;

-- Tạo tài khoản Nhân viên (mật khẩu: nhanvien123)
INSERT INTO NguoiDung (MaNguoiDung, TenDangNhap, MatKhau, HoTen, Email, GioiTinh, MaVaiTro, MaKhoaPhong, NgayBatDauLamViec, SoNgayPhepNam, SoNgayPhepDaDung, TrangThai) VALUES
('NV001', 'nhanvien1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn Nhân Viên', 'nhanvien1@school.edu.vn', 'Nam', 5, @khoa_cntt, '2021-01-15', 12, 3.5, 'Hoat_dong'),
('NV002', 'nhanvien2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị B', 'nhanvien2@school.edu.vn', 'Nu', 5, @khoa_kt, '2022-06-01', 12, 2.0, 'Hoat_dong');

-- Tạo tài khoản Giảng viên (mật khẩu: giangvien123)
INSERT INTO NguoiDung (MaNguoiDung, TenDangNhap, MatKhau, HoTen, Email, GioiTinh, MaVaiTro, MaKhoaPhong, NgayBatDauLamViec, TrangThai) VALUES
('GV001', 'giangvien1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Văn Giảng Viên', 'giangvien1@school.edu.vn', 'Nam', 6, @khoa_cntt, '2020-09-01', 'Hoat_dong');

-- Insert loại phép đã có trong database.sql nên không cần insert lại

-- Thông báo
SELECT 'Dữ liệu test đã được thêm thành công!' as Message;
SELECT 'Tài khoản test:' as Info;
SELECT 'Admin: admin / admin123' as Account;
SELECT 'Phó HT: phohieutruong / pht123' as Account;
SELECT 'Trưởng khoa: truongkhoa_cntt / truongkhoa123' as Account;
SELECT 'Nhân viên: nhanvien1 / nhanvien123' as Account;
SELECT 'Giảng viên: giangvien1 / giangvien123' as Account;