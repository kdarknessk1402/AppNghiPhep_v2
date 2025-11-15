-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 15, 2025 lúc 04:37 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `appnghiphep`
--
CREATE DATABASE IF NOT EXISTS `appnghiphep` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `appnghiphep`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhinhemail`
--

CREATE TABLE `cauhinhemail` (
  `MaCauHinh` int(11) NOT NULL,
  `SmtpHost` varchar(100) NOT NULL,
  `SmtpPort` int(11) NOT NULL,
  `SmtpUsername` varchar(100) NOT NULL,
  `SmtpPassword` varchar(255) NOT NULL,
  `EmailNguoiGui` varchar(100) NOT NULL,
  `TenNguoiGui` varchar(100) NOT NULL,
  `EmailNhan` varchar(100) NOT NULL,
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cauhinhemail`
--

INSERT INTO `cauhinhemail` (`MaCauHinh`, `SmtpHost`, `SmtpPort`, `SmtpUsername`, `SmtpPassword`, `EmailNguoiGui`, `TenNguoiGui`, `EmailNhan`, `NgayCapNhat`) VALUES
(1, 'smtp.gmail.com', 587, 'thbao.thuduc@gmail.com', 'gzgiilqoihmefzve', 'thbao.thuduc@gmail.com', 'Hệ thống nghỉ phép', 'admin@school.edu.vn', '2025-11-11 07:12:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Khoa Công nghệ thông tin'),
(2, 'Phòng Hành chính'),
(3, 'Khoa Điện tử'),
(4, 'Khoa Ngoại ngữ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donnghiphep`
--

CREATE TABLE `donnghiphep` (
  `MaDon` varchar(20) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `NguoiTao` varchar(50) DEFAULT NULL,
  `LoaiPhep` varchar(50) NOT NULL,
  `LoaiDon` enum('Phep_thuong','Nghi_bu') DEFAULT 'Phep_thuong',
  `MaNghiBu` int(11) DEFAULT NULL,
  `NgayBatDauNghi` date NOT NULL,
  `NghiNuaNgayBatDau` enum('Khong','Sang','Chieu') DEFAULT 'Khong',
  `NgayKetThucNghi` date NOT NULL,
  `NghiNuaNgayKetThuc` enum('Khong','Sang','Chieu') DEFAULT 'Khong',
  `SoNgayNghi` decimal(5,1) NOT NULL,
  `LyDo` varchar(100) NOT NULL,
  `TrangThai` enum('WAITING','ACCEPT','DENY') DEFAULT 'WAITING',
  `GhiChuAdmin` varchar(100) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `TinhVaoPhepNam` tinyint(1) DEFAULT 1 COMMENT '1=Tính vào phép năm, 0=Không tính (vd: thai sản)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donnghiphep`
--

INSERT INTO `donnghiphep` (`MaDon`, `MaNguoiDung`, `NguoiTao`, `LoaiPhep`, `LoaiDon`, `MaNghiBu`, `NgayBatDauNghi`, `NghiNuaNgayBatDau`, `NgayKetThucNghi`, `NghiNuaNgayKetThuc`, `SoNgayNghi`, `LyDo`, `TrangThai`, `GhiChuAdmin`, `NgayTao`, `NgayCapNhat`, `TinhVaoPhepNam`) VALUES
('DN202511111853', 'USER001', 'USER001', 'Phép ốm', 'Phep_thuong', NULL, '2025-11-17', 'Khong', '2025-11-19', 'Khong', 3.0, 'acsaccscs', 'DENY', 'asdada', '2025-11-11 07:15:38', '2025-11-12 00:33:30', 1),
('DN202511111863', 'USER001', 'USER001', 'Phép năm', 'Phep_thuong', NULL, '2025-11-11', 'Khong', '2025-11-12', 'Khong', 2.0, 'asda', 'ACCEPT', 'duyet', '2025-11-11 06:54:47', '2025-11-12 00:33:30', 1),
('DN202511112426', 'USER001', 'USER001', 'Phép năm', 'Phep_thuong', NULL, '2025-11-21', 'Khong', '2025-11-22', 'Khong', 2.0, 'thich thi nghi', 'ACCEPT', 'asdad', '2025-11-11 07:19:41', '2025-11-12 00:33:30', 1),
('DN202511115498', 'USER001', 'USER001', 'Phép năm', 'Phep_thuong', NULL, '2025-11-14', 'Khong', '2025-11-18', 'Khong', 5.0, 'Abc', 'DENY', 'asda', '2025-11-11 07:02:34', '2025-11-12 00:33:30', 1),
('DN202511123576', 'USER002', 'USER002', 'Phép năm', 'Phep_thuong', NULL, '2025-11-12', 'Sang', '2025-11-13', 'Khong', 1.5, 'abc', 'WAITING', NULL, '2025-11-12 00:37:07', '2025-11-12 00:37:07', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichdaybu`
--

CREATE TABLE `lichdaybu` (
  `MaLich` int(11) NOT NULL,
  `MaDon` varchar(20) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `NgayDayBanDau` date NOT NULL,
  `BuoiDayBanDau` varchar(10) NOT NULL,
  `LopHocBanDau` varchar(10) NOT NULL,
  `MonHocBanDau` varchar(100) NOT NULL,
  `NgayDayBu` date NOT NULL,
  `BuoiDayBu` varchar(10) NOT NULL,
  `LopHocBu` varchar(10) NOT NULL,
  `MonHocBu` varchar(100) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichsuemail`
--

CREATE TABLE `lichsuemail` (
  `MaLichSu` int(11) NOT NULL,
  `MaDon` varchar(20) NOT NULL,
  `EmailNhan` varchar(100) NOT NULL,
  `TieuDeEmail` varchar(255) NOT NULL,
  `TrangThai` enum('Thanh_cong','That_bai') NOT NULL,
  `ThongBaoLoi` varchar(255) DEFAULT NULL,
  `ThoiGianGui` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lichsuemail`
--

INSERT INTO `lichsuemail` (`MaLichSu`, `MaDon`, `EmailNhan`, `TieuDeEmail`, `TrangThai`, `ThongBaoLoi`, `ThoiGianGui`) VALUES
(1, 'DN202511111863', 'Array', '[APPNGHIPHEP] Đơn nghỉ phép mới - DN202511111863', 'That_bai', 'Lỗi gửi email: SMTP Error: Could not authenticate.', '2025-11-11 06:54:50'),
(2, 'DN202511111863', 'user1@school.edu.vn', '[APPNGHIPHEP] Đơn nghỉ phép đã được duyệt - DN202511111863', 'That_bai', 'Lỗi gửi email: SMTP Error: Could not authenticate.', '2025-11-11 06:55:27'),
(3, 'DN202511115498', 'thbao.thuduc@gmail.com', '[ĐƠN NGHỈ PHÉP] Trần Thị User - DN202511115498', 'That_bai', 'SMTP Error: Could not authenticate.', '2025-11-11 07:02:37'),
(4, 'DN202511115498', 'thbao.thuduc@gmail.com', '[TỪ CHỐI] Đơn nghỉ phép DN202511115498 bị từ chối', 'Thanh_cong', NULL, '2025-11-11 07:15:16'),
(5, 'DN202511111853', 'kdarknessk1402@gmail.com', '[ĐƠN NGHỈ PHÉP] Trần Thị User - DN202511111853', 'Thanh_cong', NULL, '2025-11-11 07:15:42'),
(6, 'DN202511112426', 'kdarknessk1402@gmail.com', '[ĐƠN NGHỈ PHÉP] Trần Thị User - DN202511112426', 'Thanh_cong', NULL, '2025-11-11 07:19:46'),
(7, 'DN202511112426', 'thbao.thuduc@gmail.com', '[PHÊ DUYỆT] Đơn nghỉ phép DN202511112426 đã được duyệt', 'Thanh_cong', NULL, '2025-11-11 07:20:31'),
(8, 'DN202511111853', 'thbao.thuduc@gmail.com', '[TỪ CHỐI] Đơn nghỉ phép DN202511111853 bị từ chối', 'Thanh_cong', NULL, '2025-11-11 07:20:41'),
(9, 'DN202511123576', 'manager2@school.edu.vn, kdarknessk1402@gmail.com', '[ĐƠN NGHỈ PHÉP] Nguyễn Thị B - DN202511123576', 'Thanh_cong', NULL, '2025-11-12 00:37:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichsuphepton`
--

CREATE TABLE `lichsuphepton` (
  `MaLichSu` int(11) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `Nam` int(11) NOT NULL,
  `SoNgayPhepDuocCap` decimal(5,1) NOT NULL,
  `SoNgayPhepDaSuDung` decimal(5,1) DEFAULT 0.0,
  `SoNgayPhepDu` decimal(5,1) DEFAULT 0.0,
  `SoNgayPhepTonChuyenSangNamSau` decimal(5,1) DEFAULT 0.0,
  `SoNgayPhepTonDaSuDungNamSau` decimal(5,1) DEFAULT 0.0,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nghibu`
--

CREATE TABLE `nghibu` (
  `MaNghiBu` int(11) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `LoaiNghiBu` enum('Nghi_truoc_lam_sau','Lam_truoc_nghi_sau') NOT NULL,
  `NgayNghiBu` date NOT NULL,
  `SoNgayNghi` decimal(5,1) NOT NULL DEFAULT 1.0,
  `NgayLamBu` date DEFAULT NULL,
  `SoNgayLam` decimal(5,1) DEFAULT 0.0,
  `TrangThai` enum('Cho_lam_bu','Da_lam_bu','Qua_han') DEFAULT 'Cho_lam_bu',
  `LyDo` text DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `NguoiDuyet` varchar(50) DEFAULT NULL,
  `TrangThaiDuyet` enum('WAITING','ACCEPT','DENY') DEFAULT 'WAITING',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nghibu`
--

INSERT INTO `nghibu` (`MaNghiBu`, `MaNguoiDung`, `LoaiNghiBu`, `NgayNghiBu`, `SoNgayNghi`, `NgayLamBu`, `SoNgayLam`, `TrangThai`, `LyDo`, `GhiChu`, `NguoiDuyet`, `TrangThaiDuyet`, `NgayTao`, `NgayCapNhat`) VALUES
(1, 'USER001', 'Lam_truoc_nghi_sau', '2025-12-02', 1.0, '2025-11-30', 0.0, 'Cho_lam_bu', 'Làm thêm ngày cuối tuần để hoàn thành dự án', NULL, NULL, 'ACCEPT', '2025-11-12 06:13:33', '2025-11-12 06:13:33'),
(2, 'USER001', 'Nghi_truoc_lam_sau', '2025-12-10', 1.0, '2025-12-14', 0.0, 'Cho_lam_bu', 'Có việc gia đình đột xuất, sẽ làm bù cuối tuần', NULL, NULL, 'WAITING', '2025-11-12 06:13:33', '2025-11-12 06:13:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaNguoiDung` varchar(50) NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `GioiTinh` enum('Nam','Nu') DEFAULT 'Nam',
  `ViTri` varchar(100) NOT NULL,
  `KhoaPhong` varchar(100) DEFAULT NULL,
  `MaVaiTro` int(11) NOT NULL DEFAULT 2,
  `NamBatDauLamViec` datetime DEFAULT NULL,
  `SoNgayPhepNam` int(11) DEFAULT 12,
  `SoNgayPhepDaDung` decimal(5,1) DEFAULT 0.0,
  `SoNgayPhepTonNamTruoc` decimal(5,1) DEFAULT 0.0 COMMENT 'Số ngày phép tồn từ năm trước (chỉ dùng được trong Q1)',
  `NamPhepTon` int(11) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaNguoiDung`, `TenDangNhap`, `MatKhau`, `HoTen`, `Email`, `GioiTinh`, `ViTri`, `KhoaPhong`, `MaVaiTro`, `NamBatDauLamViec`, `SoNgayPhepNam`, `SoNgayPhepDaDung`, `SoNgayPhepTonNamTruoc`, `NamPhepTon`, `NgayTao`) VALUES
('ADMIN001', 'admin', '$2y$10$CbSC6D2gAraB73C.aI6oxuQ0FIbORrOsAnMqL.eqXWBPsC2nTsPtW', 'Nguyễn Văn Admin', 'kdarknessk1402@gmail.com', 'Nam', 'Quản trị viên', 'Phòng Đào tạo', 1, '2020-01-01 00:00:00', 12, 0.0, 0.0, NULL, '2025-11-10 09:16:36'),
('MGR001', 'manager', '$2y$10$BT6yImKCAbgJ5swoRChxweMAy6Vk4vWY4N9354qfCJsHcIJdiVyJe', 'Lê Văn Quản lý', 'kdarknessk1402@gmail.com', 'Nam', 'Trưởng khoa', 'Khoa Công nghệ', 3, '2019-09-01 00:00:00', 12, 0.0, 0.0, NULL, '2025-11-10 09:16:36'),
('MGR002', 'manager2', '$2y$10$veApIBNhO3tnvxPYhlcTyedMReOcOKNy.dpW1YGpCrUhggC4Xd8.y', 'Trần Văn Manager 2', 'manager2@school.edu.vn', 'Nam', 'Trưởng khoa', 'Khoa Kinh tế', 3, '2020-01-01 00:00:00', 12, 0.0, 0.0, NULL, '2025-11-12 00:33:30'),
('USER001', 'user1', '$2y$10$weY6fDQ0faZzBkEY32lK3u3IIKTEASoGccyvMMYkJItgxTqD7XdQm', 'Trần Thị User', 'thbao.thuduc@gmail.com', 'Nu', 'Giảng viên', 'Khoa Công nghệ', 2, '2021-06-15 00:00:00', 12, 8.0, 4.0, 2024, '2025-11-10 09:16:36'),
('USER002', 'user2', '$2y$10$dpgK3djqPuUUrKb5kKeUX.Yxmt2Em.M..YFZdfw4lLsnQVUkFztUe', 'Nguyễn Thị B', 'user2@school.edu.vn', 'Nu', '', 'Khoa Công nghệ thông tin', 2, '2022-01-01 00:00:00', 12, 0.0, 0.0, NULL, '2025-11-12 00:33:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Giảng Viên'),
(2, 'Nhân viên hành chính'),
(3, 'Trưởng khoa/phòng'),
(4, 'Phó hiệu trưởng Quản lý'),
(5, 'Trưởng phòng hành chính');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `MaVaiTro` int(11) NOT NULL,
  `TenVaiTro` varchar(50) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`MaVaiTro`, `TenVaiTro`, `MoTa`, `NgayTao`) VALUES
(1, 'ADMIN', 'Quản trị viên - Toàn quyền hệ thống', '2025-11-10 09:16:36'),
(2, 'USER', 'Người dùng - Nhân viên thông thường', '2025-11-10 09:16:36'),
(3, 'MANAGER', 'Quản lý - Duyệt đơn, tạo đơn cho nhân viên, quản lý nghỉ bù', '2025-11-10 09:16:36');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_thongkephep`
-- (See below for the actual view)
--
CREATE TABLE `v_thongkephep` (
`MaNguoiDung` varchar(50)
,`HoTen` varchar(100)
,`KhoaPhong` varchar(100)
,`SoNgayPhepNam` int(11)
,`SoNgayPhepDaDung` decimal(5,1)
,`SoNgayPhepTonNamTruoc` decimal(5,1)
,`NamPhepTon` int(11)
,`PhepConLaiNamNay` decimal(12,1)
,`PhepTonConDungDuoc` decimal(5,1)
,`TongPhepCoTheDung` decimal(13,1)
,`SoLanChoLamBu` bigint(21)
,`SoNgayChoLamBu` decimal(27,1)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_thongkephep`
--
DROP TABLE IF EXISTS `v_thongkephep`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_thongkephep`  AS SELECT `n`.`MaNguoiDung` AS `MaNguoiDung`, `n`.`HoTen` AS `HoTen`, `n`.`KhoaPhong` AS `KhoaPhong`, `n`.`SoNgayPhepNam` AS `SoNgayPhepNam`, `n`.`SoNgayPhepDaDung` AS `SoNgayPhepDaDung`, `n`.`SoNgayPhepTonNamTruoc` AS `SoNgayPhepTonNamTruoc`, `n`.`NamPhepTon` AS `NamPhepTon`, `n`.`SoNgayPhepNam`- `n`.`SoNgayPhepDaDung` AS `PhepConLaiNamNay`, CASE WHEN month(curdate()) <= 3 AND `n`.`NamPhepTon` = year(curdate()) - 1 THEN `n`.`SoNgayPhepTonNamTruoc` ELSE 0 END AS `PhepTonConDungDuoc`, `n`.`SoNgayPhepNam`- `n`.`SoNgayPhepDaDung` + CASE WHEN month(curdate()) <= 3 AND `n`.`NamPhepTon` = year(curdate()) - 1 THEN `n`.`SoNgayPhepTonNamTruoc` ELSE 0 END AS `TongPhepCoTheDung`, (select count(0) from `nghibu` where `nghibu`.`MaNguoiDung` = `n`.`MaNguoiDung` and `nghibu`.`TrangThai` = 'Cho_lam_bu') AS `SoLanChoLamBu`, (select sum(`nghibu`.`SoNgayNghi`) from `nghibu` where `nghibu`.`MaNguoiDung` = `n`.`MaNguoiDung` and `nghibu`.`TrangThai` = 'Cho_lam_bu') AS `SoNgayChoLamBu` FROM (`nguoidung` `n` join `vaitro` `v` on(`n`.`MaVaiTro` = `v`.`MaVaiTro`)) WHERE `v`.`TenVaiTro` = 'USER' ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cauhinhemail`
--
ALTER TABLE `cauhinhemail`
  ADD PRIMARY KEY (`MaCauHinh`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `donnghiphep`
--
ALTER TABLE `donnghiphep`
  ADD PRIMARY KEY (`MaDon`),
  ADD KEY `MaNguoiDung` (`MaNguoiDung`),
  ADD KEY `NguoiTao` (`NguoiTao`),
  ADD KEY `MaNghiBu` (`MaNghiBu`),
  ADD KEY `idx_tinh_phep` (`TinhVaoPhepNam`);

--
-- Chỉ mục cho bảng `lichdaybu`
--
ALTER TABLE `lichdaybu`
  ADD PRIMARY KEY (`MaLich`),
  ADD KEY `MaDon` (`MaDon`),
  ADD KEY `MaNguoiDung` (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `lichsuemail`
--
ALTER TABLE `lichsuemail`
  ADD PRIMARY KEY (`MaLichSu`),
  ADD KEY `MaDon` (`MaDon`);

--
-- Chỉ mục cho bảng `lichsuphepton`
--
ALTER TABLE `lichsuphepton`
  ADD PRIMARY KEY (`MaLichSu`),
  ADD UNIQUE KEY `unique_user_year` (`MaNguoiDung`,`Nam`);

--
-- Chỉ mục cho bảng `nghibu`
--
ALTER TABLE `nghibu`
  ADD PRIMARY KEY (`MaNghiBu`),
  ADD KEY `NguoiDuyet` (`NguoiDuyet`),
  ADD KEY `idx_nguoi_dung` (`MaNguoiDung`),
  ADD KEY `idx_trang_thai` (`TrangThai`),
  ADD KEY `idx_ngay_nghi_bu` (`NgayNghiBu`),
  ADD KEY `idx_ngay_lam_bu` (`NgayLamBu`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaNguoiDung`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `MaVaiTro` (`MaVaiTro`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`MaVaiTro`),
  ADD UNIQUE KEY `TenVaiTro` (`TenVaiTro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cauhinhemail`
--
ALTER TABLE `cauhinhemail`
  MODIFY `MaCauHinh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `lichdaybu`
--
ALTER TABLE `lichdaybu`
  MODIFY `MaLich` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lichsuemail`
--
ALTER TABLE `lichsuemail`
  MODIFY `MaLichSu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `lichsuphepton`
--
ALTER TABLE `lichsuphepton`
  MODIFY `MaLichSu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nghibu`
--
ALTER TABLE `nghibu`
  MODIFY `MaNghiBu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `MaVaiTro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `donnghiphep`
--
ALTER TABLE `donnghiphep`
  ADD CONSTRAINT `donnghiphep_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `donnghiphep_ibfk_2` FOREIGN KEY (`NguoiTao`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL,
  ADD CONSTRAINT `donnghiphep_ibfk_3` FOREIGN KEY (`MaNghiBu`) REFERENCES `nghibu` (`MaNghiBu`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lichdaybu`
--
ALTER TABLE `lichdaybu`
  ADD CONSTRAINT `lichdaybu_ibfk_1` FOREIGN KEY (`MaDon`) REFERENCES `donnghiphep` (`MaDon`) ON DELETE CASCADE,
  ADD CONSTRAINT `lichdaybu_ibfk_2` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lichsuemail`
--
ALTER TABLE `lichsuemail`
  ADD CONSTRAINT `lichsuemail_ibfk_1` FOREIGN KEY (`MaDon`) REFERENCES `donnghiphep` (`MaDon`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lichsuphepton`
--
ALTER TABLE `lichsuphepton`
  ADD CONSTRAINT `lichsuphepton_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nghibu`
--
ALTER TABLE `nghibu`
  ADD CONSTRAINT `nghibu_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `nghibu_ibfk_2` FOREIGN KEY (`NguoiDuyet`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`MaVaiTro`) REFERENCES `vaitro` (`MaVaiTro`);
--
-- Cơ sở dữ liệu: `appnghiphep_v2`
--
CREATE DATABASE IF NOT EXISTS `appnghiphep_v2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `appnghiphep_v2`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baonghibaobu`
--

CREATE TABLE `baonghibaobu` (
  `MaBaoNghi` int(11) NOT NULL,
  `MaGiangVien` varchar(50) NOT NULL,
  `MaLop` int(11) DEFAULT NULL,
  `MaMon` int(11) DEFAULT NULL,
  `NgayNghi` date NOT NULL,
  `BuoiNghi` enum('Sang','Chieu','Toi') NOT NULL,
  `NgayBu` date DEFAULT NULL,
  `BuoiBu` enum('Sang','Chieu','Toi') DEFAULT NULL,
  `LyDo` text DEFAULT NULL,
  `TrangThai` enum('Cho_duyet','Da_duyet','Tu_choi') DEFAULT 'Cho_duyet',
  `NguoiDuyet` varchar(50) DEFAULT NULL,
  `NgayDuyet` datetime DEFAULT NULL,
  `GhiChu` text DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhinhemail`
--

CREATE TABLE `cauhinhemail` (
  `MaCauHinh` int(11) NOT NULL,
  `SmtpHost` varchar(100) NOT NULL,
  `SmtpPort` int(11) NOT NULL DEFAULT 587,
  `SmtpUsername` varchar(100) NOT NULL,
  `SmtpPassword` varchar(255) NOT NULL,
  `EmailNguoiGui` varchar(100) NOT NULL,
  `TenNguoiGui` varchar(100) NOT NULL,
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cauhinhemail`
--

INSERT INTO `cauhinhemail` (`MaCauHinh`, `SmtpHost`, `SmtpPort`, `SmtpUsername`, `SmtpPassword`, `EmailNguoiGui`, `TenNguoiGui`, `NgayCapNhat`) VALUES
(1, 'smtp.gmail.com', 587, 'your-email@gmail.com', 'your-app-password', 'noreply@school.edu.vn', 'Hệ thống Nghỉ Phép', '2025-11-15 03:37:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhinhhethong`
--

CREATE TABLE `cauhinhhethong` (
  `MaCauHinh` int(11) NOT NULL,
  `TenCauHinh` varchar(100) NOT NULL,
  `GiaTri` text NOT NULL,
  `MoTa` text DEFAULT NULL,
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cauhinhhethong`
--

INSERT INTO `cauhinhhethong` (`MaCauHinh`, `TenCauHinh`, `GiaTri`, `MoTa`, `NgayCapNhat`) VALUES
(1, 'GioiHanNghiBuThang', '1', 'Số tháng giới hạn hoàn thành nghỉ bù/làm bù', '2025-11-15 03:37:28'),
(2, 'SoNgayPhepMacDinh', '12', 'Số ngày phép năm mặc định cho nhân viên mới', '2025-11-15 03:37:28'),
(3, 'PhienBanPhanMem', '2.0.0', 'Phiên bản hiện tại của phần mềm', '2025-11-15 03:37:28'),
(4, 'SoNgayThongBaoTruoc', '3', 'Số ngày phải thông báo trước khi nghỉ phép', '2025-11-15 03:37:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamcong`
--

CREATE TABLE `chamcong` (
  `MaChamCong` int(11) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `Thang` int(11) NOT NULL,
  `Nam` int(11) NOT NULL,
  `NgayLamViec` date NOT NULL,
  `KyHieu` varchar(5) NOT NULL COMMENT 'X=Công, P=Phép, K=Không lương, O=Ốm, T=Trễ',
  `GhiChu` varchar(255) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donnghiphep`
--

CREATE TABLE `donnghiphep` (
  `MaDon` varchar(20) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `NguoiTao` varchar(50) DEFAULT NULL,
  `MaLoaiPhep` int(11) DEFAULT NULL,
  `LoaiPhep` enum('Tinh_phep_nam','Khong_tinh_phep_nam') DEFAULT 'Tinh_phep_nam',
  `TenLoaiPhep` varchar(100) DEFAULT NULL,
  `NgayBatDauNghi` date NOT NULL,
  `BuoiNghiBatDau` enum('Ca_ngay','Buoi_sang','Buoi_chieu') DEFAULT 'Ca_ngay',
  `NgayKetThucNghi` date NOT NULL,
  `BuoiNghiKetThuc` enum('Ca_ngay','Buoi_sang','Buoi_chieu') DEFAULT 'Ca_ngay',
  `SoNgayNghi` decimal(5,1) NOT NULL,
  `LyDo` text DEFAULT NULL,
  `NguoiBanGiaoCongViec` varchar(100) DEFAULT NULL,
  `TrangThai` enum('Cho_duyet_cap_1','Cho_duyet_cap_2','Cho_duyet_cap_3','Da_duyet','Tu_choi') DEFAULT 'Cho_duyet_cap_1',
  `CapDuyetHienTai` int(11) DEFAULT 1 COMMENT '1=Trưởng phòng, 2=Phó HT, 3=Hiệu trưởng',
  `NguoiDuyetCap1` varchar(50) DEFAULT NULL,
  `NgayDuyetCap1` datetime DEFAULT NULL,
  `GhiChuCap1` text DEFAULT NULL,
  `TrangThaiCap1` enum('Cho_duyet','Dong_y','Tu_choi') DEFAULT 'Cho_duyet',
  `NguoiDuyetCap2` varchar(50) DEFAULT NULL,
  `NgayDuyetCap2` datetime DEFAULT NULL,
  `GhiChuCap2` text DEFAULT NULL,
  `TrangThaiCap2` enum('Cho_duyet','Dong_y','Tu_choi') DEFAULT 'Cho_duyet',
  `NguoiDuyetCap3` varchar(50) DEFAULT NULL,
  `NgayDuyetCap3` datetime DEFAULT NULL,
  `GhiChuCap3` text DEFAULT NULL,
  `TrangThaiCap3` enum('Cho_duyet','Dong_y','Tu_choi') DEFAULT 'Cho_duyet',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoaphong`
--

CREATE TABLE `khoaphong` (
  `MaKhoaPhong` int(11) NOT NULL,
  `TenKhoaPhong` varchar(100) NOT NULL,
  `LoaiDonVi` enum('Khoa','Phong') DEFAULT 'Khoa',
  `MaTruongPhong` varchar(50) DEFAULT NULL COMMENT 'Mã người quản lý',
  `Email` varchar(100) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `TrangThai` enum('Hoat_dong','Ngung_hoat_dong') DEFAULT 'Hoat_dong',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khoaphong`
--

INSERT INTO `khoaphong` (`MaKhoaPhong`, `TenKhoaPhong`, `LoaiDonVi`, `MaTruongPhong`, `Email`, `SoDienThoai`, `TrangThai`, `NgayTao`) VALUES
(1, 'Khoa Công nghệ Thông tin', 'Khoa', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27'),
(2, 'Khoa Kinh tế', 'Khoa', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27'),
(3, 'Khoa Ngoại ngữ', 'Khoa', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27'),
(4, 'Phòng Hành chính Tổ chức', 'Phong', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27'),
(5, 'Phòng Đào tạo', 'Phong', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27'),
(6, 'Phòng Tài chính Kế toán', 'Phong', NULL, NULL, NULL, 'Hoat_dong', '2025-11-15 03:37:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichsuemail`
--

CREATE TABLE `lichsuemail` (
  `MaLichSu` int(11) NOT NULL,
  `MaDonLienQuan` varchar(20) DEFAULT NULL,
  `EmailNguoiNhan` varchar(100) NOT NULL,
  `TieuDe` varchar(255) NOT NULL,
  `NoiDung` text DEFAULT NULL,
  `TrangThai` enum('Thanh_cong','That_bai') NOT NULL,
  `ThongBaoLoi` text DEFAULT NULL,
  `ThoiGianGui` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaiphepkhongtinhphepnam`
--

CREATE TABLE `loaiphepkhongtinhphepnam` (
  `MaLoaiPhep` int(11) NOT NULL,
  `TenLoaiPhep` varchar(100) NOT NULL,
  `KyHieuChamCong` varchar(5) DEFAULT 'K',
  `SoNgayMacDinh` int(11) DEFAULT 0 COMMENT 'Số ngày mặc định (VD: Thai sản = 180)',
  `MoTa` text DEFAULT NULL,
  `GioiTinh` enum('Nam','Nu','Tat_ca') DEFAULT 'Tat_ca',
  `TrangThai` enum('Hoat_dong','Ngung_hoat_dong') DEFAULT 'Hoat_dong',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaiphepkhongtinhphepnam`
--

INSERT INTO `loaiphepkhongtinhphepnam` (`MaLoaiPhep`, `TenLoaiPhep`, `KyHieuChamCong`, `SoNgayMacDinh`, `MoTa`, `GioiTinh`, `TrangThai`, `NgayTao`) VALUES
(1, 'Phép thai sản', 'T', 180, 'Nghỉ thai sản 6 tháng', 'Nu', 'Hoat_dong', '2025-11-15 03:37:27'),
(2, 'Phép hiếu', 'H', 3, 'Nghỉ tang gia đình', 'Tat_ca', 'Hoat_dong', '2025-11-15 03:37:27'),
(3, 'Phép hỷ', 'HY', 3, 'Nghỉ đám cưới', 'Tat_ca', 'Hoat_dong', '2025-11-15 03:37:27'),
(4, 'Phép không lương', 'K', 0, 'Nghỉ không hưởng lương', 'Tat_ca', 'Hoat_dong', '2025-11-15 03:37:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaipheptinhphepnam`
--

CREATE TABLE `loaipheptinhphepnam` (
  `MaLoaiPhep` int(11) NOT NULL,
  `TenLoaiPhep` varchar(100) NOT NULL,
  `KyHieuChamCong` varchar(5) DEFAULT 'P',
  `MoTa` text DEFAULT NULL,
  `TrangThai` enum('Hoat_dong','Ngung_hoat_dong') DEFAULT 'Hoat_dong',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaipheptinhphepnam`
--

INSERT INTO `loaipheptinhphepnam` (`MaLoaiPhep`, `TenLoaiPhep`, `KyHieuChamCong`, `MoTa`, `TrangThai`, `NgayTao`) VALUES
(1, 'Phép năm', 'P', 'Nghỉ phép năm thông thường', 'Hoat_dong', '2025-11-15 03:37:27'),
(2, 'Phép ốm', 'O', 'Nghỉ ốm đau bệnh tật', 'Hoat_dong', '2025-11-15 03:37:27'),
(3, 'Phép việc riêng', 'V', 'Nghỉ giải quyết việc riêng', 'Hoat_dong', '2025-11-15 03:37:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loghoatdong`
--

CREATE TABLE `loghoatdong` (
  `MaLog` int(11) NOT NULL,
  `MaNguoiDung` varchar(50) DEFAULT NULL,
  `HanhDong` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `IpAddress` varchar(50) DEFAULT NULL,
  `UserAgent` text DEFAULT NULL,
  `ThoiGian` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lophoc`
--

CREATE TABLE `lophoc` (
  `MaLop` int(11) NOT NULL,
  `TenLop` varchar(50) NOT NULL,
  `KhoaHoc` varchar(20) DEFAULT NULL,
  `SiSo` int(11) DEFAULT 0,
  `MaKhoaPhong` int(11) DEFAULT NULL,
  `TrangThai` enum('Hoat_dong','Ngung_hoat_dong') DEFAULT 'Hoat_dong',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monhoc`
--

CREATE TABLE `monhoc` (
  `MaMon` int(11) NOT NULL,
  `TenMon` varchar(100) NOT NULL,
  `SoTinChi` int(11) DEFAULT 3,
  `MaKhoaPhong` int(11) DEFAULT NULL,
  `TrangThai` enum('Hoat_dong','Ngung_hoat_dong') DEFAULT 'Hoat_dong',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nghibulambu`
--

CREATE TABLE `nghibulambu` (
  `MaNghiBu` int(11) NOT NULL,
  `MaNguoiDung` varchar(50) NOT NULL,
  `LoaiNghiBu` enum('Lam_truoc_nghi_sau','Nghi_truoc_lam_sau') NOT NULL,
  `NgayLamBu` date DEFAULT NULL COMMENT 'Ngày làm bù (T7/CN)',
  `BuoiLamBu` enum('Ca_ngay','Buoi_sang','Buoi_chieu') DEFAULT 'Ca_ngay',
  `SoNgayLamBu` decimal(5,1) DEFAULT 0.0,
  `NgayNghiBu` date DEFAULT NULL COMMENT 'Ngày nghỉ bù (T2-T6)',
  `BuoiNghiBu` enum('Ca_ngay','Buoi_sang','Buoi_chieu') DEFAULT 'Ca_ngay',
  `SoNgayNghiBu` decimal(5,1) DEFAULT 0.0,
  `LyDo` text DEFAULT NULL,
  `TrangThai` enum('Cho_xac_nhan','Da_xac_nhan','Qua_han','Huy') DEFAULT 'Cho_xac_nhan',
  `NguoiXacNhan` varchar(50) DEFAULT NULL,
  `NgayXacNhan` datetime DEFAULT NULL,
  `GhiChu` text DEFAULT NULL,
  `HanHoanThanh` date DEFAULT NULL COMMENT 'Deadline hoàn thành (1 tháng)',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaNguoiDung` varchar(50) NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) DEFAULT NULL COMMENT 'NULL khi chưa kích hoạt',
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `AppPassword` varchar(255) DEFAULT NULL COMMENT 'App Password cho email',
  `GioiTinh` enum('Nam','Nu') DEFAULT 'Nam',
  `NgaySinh` date DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `DiaChi` text DEFAULT NULL,
  `MaVaiTro` int(11) NOT NULL,
  `MaKhoaPhong` int(11) DEFAULT NULL,
  `ViTri` varchar(100) DEFAULT NULL,
  `NgayBatDauLamViec` date DEFAULT NULL,
  `SoNgayPhepNam` decimal(5,1) DEFAULT 12.0,
  `SoNgayPhepDaDung` decimal(5,1) DEFAULT 0.0,
  `SoNgayPhepTonNamTruoc` decimal(5,1) DEFAULT 0.0,
  `NamPhepTon` int(11) DEFAULT NULL,
  `TrangThai` enum('Hoat_dong','Chua_kich_hoat','Khoa') DEFAULT 'Chua_kich_hoat',
  `TokenKichHoat` varchar(64) DEFAULT NULL COMMENT 'Token để kích hoạt tài khoản',
  `TokenExpiry` datetime DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaNguoiDung`, `TenDangNhap`, `MatKhau`, `HoTen`, `Email`, `AppPassword`, `GioiTinh`, `NgaySinh`, `SoDienThoai`, `DiaChi`, `MaVaiTro`, `MaKhoaPhong`, `ViTri`, `NgayBatDauLamViec`, `SoNgayPhepNam`, `SoNgayPhepDaDung`, `SoNgayPhepTonNamTruoc`, `NamPhepTon`, `TrangThai`, `TokenKichHoat`, `TokenExpiry`, `NgayTao`, `NgayCapNhat`) VALUES
('ADMIN001', 'admin', NULL, 'Quản trị viên hệ thống', 'admin@school.edu.vn', NULL, 'Nam', NULL, NULL, NULL, 1, NULL, 'Admin', '2020-01-01', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27'),
('GV001', 'giang_vien_1', NULL, 'Hoàng Văn Giảng Viên', 'gv1@school.edu.vn', NULL, 'Nam', NULL, NULL, NULL, 6, 1, 'Giảng viên', '2020-09-01', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27'),
('HT001', 'hieu_truong', NULL, 'Nguyễn Văn Hiệu Trưởng', 'hieu_truong@school.edu.vn', NULL, 'Nam', NULL, NULL, NULL, 2, NULL, 'Hiệu trưởng', '2015-01-01', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27'),
('NV001', 'nhan_vien_1', NULL, 'Phạm Thị Nhân Viên', 'nv1@school.edu.vn', NULL, 'Nu', NULL, NULL, NULL, 5, 1, 'Nhân viên hành chính', '2021-03-15', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27'),
('PHT001', 'pho_ht_1', NULL, 'Trần Thị Phó HT', 'pho_ht@school.edu.vn', NULL, 'Nu', NULL, NULL, NULL, 3, NULL, 'Phó Hiệu trưởng', '2018-01-01', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27'),
('TP001', 'truong_phong_cntt', NULL, 'Lê Văn Trưởng Phòng', 'tp_cntt@school.edu.vn', NULL, 'Nam', NULL, NULL, NULL, 4, 1, 'Trưởng khoa CNTT', '2019-06-01', 12.0, 0.0, 0.0, NULL, 'Chua_kich_hoat', NULL, NULL, '2025-11-15 03:37:27', '2025-11-15 03:37:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `MaVaiTro` int(11) NOT NULL,
  `TenVaiTro` varchar(50) NOT NULL COMMENT 'ADMIN, PHO_HIEU_TRUONG, TRUONG_PHONG, NHAN_VIEN, GIANG_VIEN, GIAO_VU_KHOA',
  `MoTa` varchar(255) DEFAULT NULL,
  `CapDuyet` int(11) DEFAULT 0 COMMENT '0=Không duyệt, 1=Cấp 1, 2=Cấp 2, 3=Cấp 3',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`MaVaiTro`, `TenVaiTro`, `MoTa`, `CapDuyet`, `NgayTao`) VALUES
(1, 'ADMIN', 'Quản trị viên - Toàn quyền hệ thống', 0, '2025-11-15 03:37:27'),
(2, 'HIEU_TRUONG', 'Hiệu trưởng - Phê duyệt cuối cùng', 3, '2025-11-15 03:37:27'),
(3, 'PHO_HIEU_TRUONG', 'Phó Hiệu trưởng phụ trách - Duyệt cấp 2', 2, '2025-11-15 03:37:27'),
(4, 'TRUONG_PHONG', 'Trưởng phòng/khoa - Duyệt cấp 1', 1, '2025-11-15 03:37:27'),
(5, 'NHAN_VIEN', 'Nhân viên hành chính - Tạo đơn', 0, '2025-11-15 03:37:27'),
(6, 'GIANG_VIEN', 'Giảng viên - Báo nghỉ/báo bù', 0, '2025-11-15 03:37:27'),
(7, 'GIAO_VU_KHOA', 'Giáo vụ khoa - Quản lý hợp đồng thỉnh giảng', 0, '2025-11-15 03:37:27');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baonghibaobu`
--
ALTER TABLE `baonghibaobu`
  ADD PRIMARY KEY (`MaBaoNghi`),
  ADD KEY `MaGiangVien` (`MaGiangVien`),
  ADD KEY `MaLop` (`MaLop`),
  ADD KEY `MaMon` (`MaMon`);

--
-- Chỉ mục cho bảng `cauhinhemail`
--
ALTER TABLE `cauhinhemail`
  ADD PRIMARY KEY (`MaCauHinh`);

--
-- Chỉ mục cho bảng `cauhinhhethong`
--
ALTER TABLE `cauhinhhethong`
  ADD PRIMARY KEY (`MaCauHinh`),
  ADD UNIQUE KEY `TenCauHinh` (`TenCauHinh`);

--
-- Chỉ mục cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD PRIMARY KEY (`MaChamCong`),
  ADD UNIQUE KEY `unique_cham_cong` (`MaNguoiDung`,`NgayLamViec`);

--
-- Chỉ mục cho bảng `donnghiphep`
--
ALTER TABLE `donnghiphep`
  ADD PRIMARY KEY (`MaDon`),
  ADD KEY `MaNguoiDung` (`MaNguoiDung`),
  ADD KEY `NguoiTao` (`NguoiTao`),
  ADD KEY `idx_trang_thai` (`TrangThai`),
  ADD KEY `idx_ngay_tao` (`NgayTao`);

--
-- Chỉ mục cho bảng `khoaphong`
--
ALTER TABLE `khoaphong`
  ADD PRIMARY KEY (`MaKhoaPhong`);

--
-- Chỉ mục cho bảng `lichsuemail`
--
ALTER TABLE `lichsuemail`
  ADD PRIMARY KEY (`MaLichSu`);

--
-- Chỉ mục cho bảng `loaiphepkhongtinhphepnam`
--
ALTER TABLE `loaiphepkhongtinhphepnam`
  ADD PRIMARY KEY (`MaLoaiPhep`);

--
-- Chỉ mục cho bảng `loaipheptinhphepnam`
--
ALTER TABLE `loaipheptinhphepnam`
  ADD PRIMARY KEY (`MaLoaiPhep`);

--
-- Chỉ mục cho bảng `loghoatdong`
--
ALTER TABLE `loghoatdong`
  ADD PRIMARY KEY (`MaLog`),
  ADD KEY `MaNguoiDung` (`MaNguoiDung`),
  ADD KEY `idx_thoi_gian` (`ThoiGian`);

--
-- Chỉ mục cho bảng `lophoc`
--
ALTER TABLE `lophoc`
  ADD PRIMARY KEY (`MaLop`),
  ADD KEY `MaKhoaPhong` (`MaKhoaPhong`);

--
-- Chỉ mục cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD PRIMARY KEY (`MaMon`),
  ADD KEY `MaKhoaPhong` (`MaKhoaPhong`);

--
-- Chỉ mục cho bảng `nghibulambu`
--
ALTER TABLE `nghibulambu`
  ADD PRIMARY KEY (`MaNghiBu`),
  ADD KEY `MaNguoiDung` (`MaNguoiDung`),
  ADD KEY `idx_trang_thai` (`TrangThai`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaNguoiDung`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `MaVaiTro` (`MaVaiTro`),
  ADD KEY `MaKhoaPhong` (`MaKhoaPhong`),
  ADD KEY `idx_token` (`TokenKichHoat`),
  ADD KEY `idx_email` (`Email`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`MaVaiTro`),
  ADD UNIQUE KEY `TenVaiTro` (`TenVaiTro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `baonghibaobu`
--
ALTER TABLE `baonghibaobu`
  MODIFY `MaBaoNghi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cauhinhemail`
--
ALTER TABLE `cauhinhemail`
  MODIFY `MaCauHinh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `cauhinhhethong`
--
ALTER TABLE `cauhinhhethong`
  MODIFY `MaCauHinh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  MODIFY `MaChamCong` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khoaphong`
--
ALTER TABLE `khoaphong`
  MODIFY `MaKhoaPhong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `lichsuemail`
--
ALTER TABLE `lichsuemail`
  MODIFY `MaLichSu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loaiphepkhongtinhphepnam`
--
ALTER TABLE `loaiphepkhongtinhphepnam`
  MODIFY `MaLoaiPhep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `loaipheptinhphepnam`
--
ALTER TABLE `loaipheptinhphepnam`
  MODIFY `MaLoaiPhep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `loghoatdong`
--
ALTER TABLE `loghoatdong`
  MODIFY `MaLog` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lophoc`
--
ALTER TABLE `lophoc`
  MODIFY `MaLop` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  MODIFY `MaMon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nghibulambu`
--
ALTER TABLE `nghibulambu`
  MODIFY `MaNghiBu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `MaVaiTro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baonghibaobu`
--
ALTER TABLE `baonghibaobu`
  ADD CONSTRAINT `baonghibaobu_ibfk_1` FOREIGN KEY (`MaGiangVien`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `baonghibaobu_ibfk_2` FOREIGN KEY (`MaLop`) REFERENCES `lophoc` (`MaLop`) ON DELETE SET NULL,
  ADD CONSTRAINT `baonghibaobu_ibfk_3` FOREIGN KEY (`MaMon`) REFERENCES `monhoc` (`MaMon`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD CONSTRAINT `chamcong_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `donnghiphep`
--
ALTER TABLE `donnghiphep`
  ADD CONSTRAINT `donnghiphep_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `donnghiphep_ibfk_2` FOREIGN KEY (`NguoiTao`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `loghoatdong`
--
ALTER TABLE `loghoatdong`
  ADD CONSTRAINT `loghoatdong_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lophoc`
--
ALTER TABLE `lophoc`
  ADD CONSTRAINT `lophoc_ibfk_1` FOREIGN KEY (`MaKhoaPhong`) REFERENCES `khoaphong` (`MaKhoaPhong`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD CONSTRAINT `monhoc_ibfk_1` FOREIGN KEY (`MaKhoaPhong`) REFERENCES `khoaphong` (`MaKhoaPhong`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `nghibulambu`
--
ALTER TABLE `nghibulambu`
  ADD CONSTRAINT `nghibulambu_ibfk_1` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`MaVaiTro`) REFERENCES `vaitro` (`MaVaiTro`),
  ADD CONSTRAINT `nguoidung_ibfk_2` FOREIGN KEY (`MaKhoaPhong`) REFERENCES `khoaphong` (`MaKhoaPhong`) ON DELETE SET NULL;
--
-- Cơ sở dữ liệu: `giang_vien_system`
--
CREATE DATABASE IF NOT EXISTS `giang_vien_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `giang_vien_system`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bao_nghi`
--

CREATE TABLE `bao_nghi` (
  `id` int(11) NOT NULL,
  `giang_vien_id` int(11) NOT NULL,
  `lop_hoc_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) NOT NULL,
  `buoi_day` enum('Sáng','Chiều','Tối') NOT NULL,
  `ngay_nghi` date NOT NULL,
  `ngay_day_bu` date DEFAULT NULL,
  `ly_do_id` int(11) NOT NULL,
  `ghi_chu` text DEFAULT NULL,
  `nguoi_tao_id` int(11) NOT NULL,
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Từ chối') DEFAULT 'Chờ duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_log`
--

CREATE TABLE `email_log` (
  `id` int(11) NOT NULL,
  `bao_nghi_id` int(11) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `status` enum('Đã gửi','Lỗi') DEFAULT 'Đã gửi',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giang_vien`
--

CREATE TABLE `giang_vien` (
  `id` int(11) NOT NULL,
  `ma_gv` varchar(20) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `khoa_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giang_vien`
--

INSERT INTO `giang_vien` (`id`, `ma_gv`, `ho_ten`, `email`, `khoa_id`, `is_active`, `created_at`) VALUES
(1, 'GV001', 'Nguyễn Văn A', 'nguyenvana@tentruong.edu.vn', 1, 1, '2025-11-11 08:04:08'),
(2, 'GV002', 'Trần Thị B', 'tranthib@tentruong.edu.vn', 1, 1, '2025-11-11 08:04:08'),
(3, 'GV003', 'Lê Văn C', 'levanc@tentruong.edu.vn', 2, 1, '2025-11-11 08:04:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `id` int(11) NOT NULL,
  `ten_khoa` varchar(100) NOT NULL,
  `ma_khoa` varchar(20) NOT NULL,
  `truong_khoa_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`id`, `ten_khoa`, `ma_khoa`, `truong_khoa_id`, `created_at`) VALUES
(1, 'Khoa Công nghệ thông tin', 'CNTT', NULL, '2025-11-11 08:04:08'),
(2, 'Khoa Kinh tế', 'KT', NULL, '2025-11-11 08:04:08'),
(3, 'Khoa Ngoại ngữ', 'NN', NULL, '2025-11-11 08:04:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lop_hoc`
--

CREATE TABLE `lop_hoc` (
  `id` int(11) NOT NULL,
  `ma_lop` varchar(50) NOT NULL,
  `ten_lop` varchar(100) NOT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `khoa_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lop_hoc`
--

INSERT INTO `lop_hoc` (`id`, `ma_lop`, `ten_lop`, `mon_hoc_id`, `khoa_id`, `created_at`) VALUES
(1, 'CNTT01', 'Lớp Công nghệ thông tin K19', 1, 1, '2025-11-11 08:04:08'),
(2, 'CNTT02', 'Lớp Hệ thống thông tin K19', 2, 1, '2025-11-11 08:04:08'),
(3, 'KT01', 'Lớp Kinh tế K19', 3, 2, '2025-11-11 08:04:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ly_do_nghi`
--

CREATE TABLE `ly_do_nghi` (
  `id` int(11) NOT NULL,
  `ly_do` varchar(200) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ly_do_nghi`
--

INSERT INTO `ly_do_nghi` (`id`, `ly_do`, `mo_ta`) VALUES
(1, 'Ốm đau', 'Giảng viên bị ốm'),
(2, 'Công tác', 'Đi công tác'),
(3, 'Gia đình', 'Việc gia đình đột xuất'),
(4, 'Hội nghị/Hội thảo', 'Tham dự hội nghị, hội thảo'),
(5, 'Khác', 'Lý do khác');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mon_hoc`
--

CREATE TABLE `mon_hoc` (
  `id` int(11) NOT NULL,
  `ma_mon` varchar(20) NOT NULL,
  `ten_mon` varchar(200) NOT NULL,
  `so_tin_chi` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `mon_hoc`
--

INSERT INTO `mon_hoc` (`id`, `ma_mon`, `ten_mon`, `so_tin_chi`, `created_at`) VALUES
(1, 'IT001', 'Lập trình cơ bản', 3, '2025-11-11 08:04:08'),
(2, 'IT002', 'Cơ sở dữ liệu', 3, '2025-11-11 08:04:08'),
(3, 'EC001', 'Kinh tế vi mô', 3, '2025-11-11 08:04:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_code`) VALUES
(1, 'Thư ký khoa/Giáo vụ', 'THU_KY'),
(2, 'Trưởng khoa', 'TRUONG_KHOA'),
(3, 'Phòng đào tạo - tuyển sinh', 'DAO_TAO');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `khoa_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role_id`, `khoa_id`, `is_active`, `created_at`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Quản trị viên', 'admin@tentruong.edu.vn', 1, 1, 1, '2025-11-11 08:04:08'),
(2, 'truongkhoa', 'e10adc3949ba59abbe56e057f20f883e', 'Trưởng khoa CNTT', 'truongkhoa@tentruong.edu.vn', 2, 1, 1, '2025-11-11 08:04:08'),
(3, 'daotao', 'e10adc3949ba59abbe56e057f20f883e', 'Phòng Đào tạo', 'daotao@tentruong.edu.vn', 3, NULL, 1, '2025-11-11 08:04:08');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bao_nghi`
--
ALTER TABLE `bao_nghi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `giang_vien_id` (`giang_vien_id`),
  ADD KEY `lop_hoc_id` (`lop_hoc_id`),
  ADD KEY `mon_hoc_id` (`mon_hoc_id`),
  ADD KEY `ly_do_id` (`ly_do_id`),
  ADD KEY `nguoi_tao_id` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `email_log`
--
ALTER TABLE `email_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bao_nghi_id` (`bao_nghi_id`);

--
-- Chỉ mục cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_gv` (`ma_gv`),
  ADD KEY `khoa_id` (`khoa_id`);

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_khoa` (`ma_khoa`);

--
-- Chỉ mục cho bảng `lop_hoc`
--
ALTER TABLE `lop_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_lop` (`ma_lop`),
  ADD KEY `mon_hoc_id` (`mon_hoc_id`),
  ADD KEY `khoa_id` (`khoa_id`);

--
-- Chỉ mục cho bảng `ly_do_nghi`
--
ALTER TABLE `ly_do_nghi`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `mon_hoc`
--
ALTER TABLE `mon_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_mon` (`ma_mon`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_code` (`role_code`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bao_nghi`
--
ALTER TABLE `bao_nghi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `khoa`
--
ALTER TABLE `khoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `lop_hoc`
--
ALTER TABLE `lop_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `ly_do_nghi`
--
ALTER TABLE `ly_do_nghi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `mon_hoc`
--
ALTER TABLE `mon_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bao_nghi`
--
ALTER TABLE `bao_nghi`
  ADD CONSTRAINT `bao_nghi_ibfk_1` FOREIGN KEY (`giang_vien_id`) REFERENCES `giang_vien` (`id`),
  ADD CONSTRAINT `bao_nghi_ibfk_2` FOREIGN KEY (`lop_hoc_id`) REFERENCES `lop_hoc` (`id`),
  ADD CONSTRAINT `bao_nghi_ibfk_3` FOREIGN KEY (`mon_hoc_id`) REFERENCES `mon_hoc` (`id`),
  ADD CONSTRAINT `bao_nghi_ibfk_4` FOREIGN KEY (`ly_do_id`) REFERENCES `ly_do_nghi` (`id`),
  ADD CONSTRAINT `bao_nghi_ibfk_5` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `email_log`
--
ALTER TABLE `email_log`
  ADD CONSTRAINT `email_log_ibfk_1` FOREIGN KEY (`bao_nghi_id`) REFERENCES `bao_nghi` (`id`);

--
-- Các ràng buộc cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD CONSTRAINT `giang_vien_ibfk_1` FOREIGN KEY (`khoa_id`) REFERENCES `khoa` (`id`);

--
-- Các ràng buộc cho bảng `lop_hoc`
--
ALTER TABLE `lop_hoc`
  ADD CONSTRAINT `lop_hoc_ibfk_1` FOREIGN KEY (`mon_hoc_id`) REFERENCES `mon_hoc` (`id`),
  ADD CONSTRAINT `lop_hoc_ibfk_2` FOREIGN KEY (`khoa_id`) REFERENCES `khoa` (`id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
--
-- Cơ sở dữ liệu: `leave_app`
--
CREATE DATABASE IF NOT EXISTS `leave_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `leave_app`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`id`, `name`) VALUES
(1, 'Nguyễn Văn A'),
(2, 'Trần Thị B'),
(3, 'Lê Văn C'),
(4, 'Phạm Thị D');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leave_reasons`
--

CREATE TABLE `leave_reasons` (
  `id` int(11) NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `leave_reasons`
--

INSERT INTO `leave_reasons` (`id`, `reason`) VALUES
(1, 'Nghỉ ốm'),
(2, 'Nghỉ phép năm'),
(3, 'Việc gia đình'),
(4, 'Nghỉ không lương');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leave_reasons`
--
ALTER TABLE `leave_reasons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `leave_reasons`
--
ALTER TABLE `leave_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Cơ sở dữ liệu: `leave_management`
--
CREATE DATABASE IF NOT EXISTS `leave_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `leave_management`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` int(11) NOT NULL DEFAULT 587,
  `smtp_user` varchar(100) NOT NULL,
  `smtp_password` varchar(255) NOT NULL,
  `from_email` varchar(100) NOT NULL,
  `from_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `email_settings`
--

INSERT INTO `email_settings` (`id`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_password`, `from_email`, `from_name`, `created_at`, `updated_at`) VALUES
(1, 'smtp.gmail.com', 587, 'thbao.thuduc@gmail.com', 'kumnacwhhntcgrup', 'thbao.thuduc@gmail.com', 'Hệ thống Nghỉ phép', '2025-10-24 05:56:35', '2025-10-24 06:54:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_content` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `template_content`, `description`, `created_at`, `updated_at`) VALUES
(1, 'leave_request', 'Tôi tên là {$TenNhanVien} xin phép nghỉ từ {$NgayBatDau} đến {$NgayKetThuc}, Lý do: {$LyDoNghi}. Xin trưởng/phụ trách khoa giải quyết.', 'Template đơn xin nghỉ phép gửi cho quản lý', '2025-10-24 05:56:35', '2025-10-24 06:50:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `manager_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`id`, `full_name`, `email`, `department`, `created_at`, `manager_email`) VALUES
(6, 'Trần Hoàng Bảo', 'thbao.thuduc@gmail.com', 'IT', '2025-10-24 06:57:08', 'kdarknessk1402@gmail.com'),
(8, 'Mai Thị Ngọc Hân', 'hanmtn@gmail.com', 'CNTT', '2025-10-24 08:33:20', 'kdarknessk1402@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leave_reasons`
--

CREATE TABLE `leave_reasons` (
  `id` int(11) NOT NULL,
  `reason_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `leave_reasons`
--

INSERT INTO `leave_reasons` (`id`, `reason_name`, `description`, `created_at`) VALUES
(1, 'Nghỉ phép năm', 'Nghỉ phép thường niên có lương', '2025-10-24 03:44:33'),
(2, 'Nghỉ ốm', 'Nghỉ do ốm đau, bệnh tật', '2025-10-24 03:44:33'),
(3, 'Nghỉ việc riêng', 'Nghỉ do có việc cá nhân', '2025-10-24 03:44:33'),
(4, 'Nghỉ thai sản', 'Nghỉ chế độ thai sản', '2025-10-24 03:44:33'),
(5, 'Nghỉ không lương', 'Nghỉ không hưởng lương', '2025-10-24 03:44:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `reason_id`, `start_date`, `end_date`, `notes`, `status`, `created_at`) VALUES
(18, 6, 3, '2025-10-24', '2025-11-06', 'asdas', 'pending', '2025-10-24 07:14:26'),
(19, 6, 3, '2025-10-24', '2025-11-01', 'asdasd', 'pending', '2025-10-24 07:14:41'),
(20, 8, 4, '2025-10-24', '2025-11-06', 'assasdas', 'pending', '2025-10-24 08:33:34');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `leave_reasons`
--
ALTER TABLE `leave_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reason_id` (`reason_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `leave_reasons`
--
ALTER TABLE `leave_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`reason_id`) REFERENCES `leave_reasons` (`id`);
--
-- Cơ sở dữ liệu: `leave_management_system`
--
CREATE DATABASE IF NOT EXISTS `leave_management_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `leave_management_system`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bao_nghi_bao_bu`
--

CREATE TABLE `bao_nghi_bao_bu` (
  `id` int(11) NOT NULL,
  `ma_bao` varchar(50) NOT NULL,
  `giang_vien_id` int(11) NOT NULL,
  `lop_hoc_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) NOT NULL,
  `ngay_nghi` date NOT NULL,
  `buoi_nghi` enum('sang','chieu','toi') NOT NULL,
  `ngay_bu` date NOT NULL,
  `buoi_bu` enum('sang','chieu','toi') NOT NULL,
  `ly_do` text DEFAULT NULL,
  `trang_thai` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  `nguoi_duyet` int(11) DEFAULT NULL,
  `ngay_duyet` timestamp NULL DEFAULT NULL,
  `y_kien` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hinh_he_thong`
--

CREATE TABLE `cau_hinh_he_thong` (
  `id` int(11) NOT NULL,
  `ten_cau_hinh` varchar(100) NOT NULL,
  `gia_tri` text NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hinh_he_thong`
--

INSERT INTO `cau_hinh_he_thong` (`id`, `ten_cau_hinh`, `gia_tri`, `mo_ta`, `updated_at`) VALUES
(1, 'so_ngay_phep_mac_dinh', '12', 'Số ngày phép mặc định trong năm', '2025-11-15 01:11:56'),
(2, 'han_hoan_thanh_nghi_bu', '30', 'Số ngày tối đa để hoàn thành nghỉ bù/làm bù', '2025-11-15 01:11:56'),
(3, 'so_ngay_tao_don_truoc', '3', 'Số ngày phải tạo đơn trước khi nghỉ (10 ngày nếu đi nước ngoài)', '2025-11-15 01:11:56'),
(4, 'cho_phep_chuyen_phep_sang_nam_sau', '1', 'Cho phép chuyển phép năm trước sang năm sau (1: có, 0: không)', '2025-11-15 01:11:56'),
(5, 'smtp_host', 'smtp.gmail.com', 'SMTP Host', '2025-11-15 01:11:56'),
(6, 'smtp_port', '587', 'SMTP Port', '2025-11-15 01:11:56'),
(7, 'smtp_username', '', 'SMTP Username', '2025-11-15 01:11:56'),
(8, 'smtp_password', '', 'SMTP Password', '2025-11-15 01:11:56'),
(9, 'smtp_encryption', 'tls', 'SMTP Encryption (tls/ssl)', '2025-11-15 01:11:56'),
(10, 'email_from', '', 'Email gửi đi mặc định', '2025-11-15 01:11:56'),
(11, 'email_from_name', 'Hệ thống nghỉ phép', 'Tên người gửi', '2025-11-15 01:11:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cham_cong`
--

CREATE TABLE `cham_cong` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ngay_cham` date NOT NULL,
  `ky_hieu` varchar(5) NOT NULL,
  `loai_phep_id` int(11) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `nguoi_cham` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `di_tre`
--

CREATE TABLE `di_tre` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ngay_di_tre` date NOT NULL,
  `gio_di_tre` time NOT NULL,
  `so_phut_tre` int(11) NOT NULL,
  `ly_do` text DEFAULT NULL,
  `nguoi_ghi_nhan` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dm_khoa_phong`
--

CREATE TABLE `dm_khoa_phong` (
  `id` int(11) NOT NULL,
  `ma_khoa_phong` varchar(20) NOT NULL,
  `ten_khoa_phong` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `loai` enum('khoa','phong') NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dm_loai_phep`
--

CREATE TABLE `dm_loai_phep` (
  `id` int(11) NOT NULL,
  `ma_loai` varchar(50) NOT NULL,
  `ten_loai` varchar(100) NOT NULL,
  `tinh_phep_nam` tinyint(4) DEFAULT 1,
  `mau_sac` varchar(7) DEFAULT '#007bff',
  `ky_hieu_cham_cong` varchar(5) DEFAULT 'P',
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dm_loai_phep`
--

INSERT INTO `dm_loai_phep` (`id`, `ma_loai`, `ten_loai`, `tinh_phep_nam`, `mau_sac`, `ky_hieu_cham_cong`, `mo_ta`, `trang_thai`, `created_at`) VALUES
(1, 'phep_nam', 'Phép năm', 1, '#28a745', 'P', NULL, 1, '2025-11-15 01:11:55'),
(2, 'phep_om', 'Phép ốm', 1, '#ffc107', 'O', NULL, 1, '2025-11-15 01:11:55'),
(3, 'phep_viec_rieng', 'Phép việc riêng', 1, '#17a2b8', 'P', NULL, 1, '2025-11-15 01:11:55'),
(4, 'thai_san', 'Thai sản', 0, '#e83e8c', 'O', NULL, 1, '2025-11-15 01:11:55'),
(5, 'phep_hieu', 'Phép hiếu', 0, '#6c757d', 'H', NULL, 1, '2025-11-15 01:11:55'),
(6, 'phep_hy', 'Phép hỷ', 0, '#fd7e14', 'H', NULL, 1, '2025-11-15 01:11:55'),
(7, 'khong_luong', 'Nghỉ không lương', 0, '#dc3545', 'K', NULL, 1, '2025-11-15 01:11:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dm_lop_hoc`
--

CREATE TABLE `dm_lop_hoc` (
  `id` int(11) NOT NULL,
  `ma_lop` varchar(50) NOT NULL,
  `ten_lop` varchar(255) NOT NULL,
  `khoa_phong_id` int(11) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dm_mon_hoc`
--

CREATE TABLE `dm_mon_hoc` (
  `id` int(11) NOT NULL,
  `ma_mon` varchar(50) NOT NULL,
  `ten_mon` varchar(255) NOT NULL,
  `so_tin_chi` int(11) DEFAULT 3,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dm_vai_tro`
--

CREATE TABLE `dm_vai_tro` (
  `id` int(11) NOT NULL,
  `ma_vai_tro` varchar(50) NOT NULL,
  `ten_vai_tro` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `cap_duyet` int(11) DEFAULT 0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dm_vai_tro`
--

INSERT INTO `dm_vai_tro` (`id`, `ma_vai_tro`, `ten_vai_tro`, `mo_ta`, `cap_duyet`, `trang_thai`, `created_at`) VALUES
(1, 'nhan_vien', 'Nhân viên hành chính', 'Nhân viên hành chính', 0, 1, '2025-11-15 01:11:55'),
(2, 'giang_vien', 'Giảng viên', 'Giảng viên giảng dạy', 0, 1, '2025-11-15 01:11:55'),
(3, 'truong_khoa', 'Trưởng khoa', 'Trưởng khoa chuyên môn', 1, 1, '2025-11-15 01:11:55'),
(4, 'truong_phong', 'Trưởng phòng', 'Trưởng phòng hành chính', 1, 1, '2025-11-15 01:11:55'),
(5, 'pho_hieu_truong', 'Phó Hiệu trưởng', 'Phó Hiệu trưởng phụ trách', 2, 1, '2025-11-15 01:11:55'),
(6, 'hieu_truong', 'Hiệu trưởng', 'Hiệu trưởng', 3, 1, '2025-11-15 01:11:55'),
(7, 'giao_vu_khoa', 'Giáo vụ khoa', 'Giáo vụ khoa', 0, 1, '2025-11-15 01:11:55'),
(8, 'admin', 'Admin hệ thống', 'Quản trị viên hệ thống', 3, 1, '2025-11-15 01:11:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_nghi_phep`
--

CREATE TABLE `don_nghi_phep` (
  `id` int(11) NOT NULL,
  `ma_don` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loai_phep_id` int(11) NOT NULL,
  `ngay_bat_dau` date NOT NULL,
  `buoi_bat_dau` enum('ca_ngay','buoi_sang','buoi_chieu') DEFAULT 'ca_ngay',
  `ngay_ket_thuc` date NOT NULL,
  `buoi_ket_thuc` enum('ca_ngay','buoi_sang','buoi_chieu') DEFAULT 'ca_ngay',
  `so_ngay_nghi` decimal(4,1) NOT NULL,
  `ly_do` text DEFAULT NULL,
  `nguoi_ban_giao` varchar(255) DEFAULT NULL,
  `file_dinh_kem` varchar(255) DEFAULT NULL,
  `trang_thai` enum('cho_duyet','cap_don_vi_duyet','cap_pht_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  `nguoi_duyet_cap_don_vi` int(11) DEFAULT NULL,
  `ngay_duyet_cap_don_vi` timestamp NULL DEFAULT NULL,
  `y_kien_cap_don_vi` text DEFAULT NULL,
  `nguoi_duyet_cap_pht` int(11) DEFAULT NULL,
  `ngay_duyet_cap_pht` timestamp NULL DEFAULT NULL,
  `y_kien_cap_pht` text DEFAULT NULL,
  `nguoi_duyet_cuoi` int(11) DEFAULT NULL,
  `ngay_duyet_cuoi` timestamp NULL DEFAULT NULL,
  `y_kien_cuoi` text DEFAULT NULL,
  `ly_do_tu_choi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong_thinh_giang`
--

CREATE TABLE `hop_dong_thinh_giang` (
  `id` int(11) NOT NULL,
  `ma_hop_dong` varchar(50) NOT NULL,
  `giang_vien_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) NOT NULL,
  `so_tien_moi_gio` decimal(10,2) NOT NULL,
  `so_gio_day` int(11) NOT NULL,
  `tong_tien` decimal(12,2) NOT NULL,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `noi_dung_hop_dong` text DEFAULT NULL,
  `file_hop_dong` varchar(255) DEFAULT NULL,
  `trang_thai` enum('moi','dang_thuc_hien','hoan_thanh','huy') DEFAULT 'moi',
  `nguoi_tao` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_mail`
--

CREATE TABLE `lich_su_mail` (
  `id` int(11) NOT NULL,
  `nguoi_gui` int(11) DEFAULT NULL,
  `email_nguoi_nhan` varchar(255) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text NOT NULL,
  `loai_mail` varchar(50) DEFAULT NULL,
  `tham_chieu_id` int(11) DEFAULT NULL,
  `tham_chieu_loai` varchar(50) DEFAULT NULL,
  `trang_thai` enum('thanh_cong','that_bai') DEFAULT 'thanh_cong',
  `loi_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nghi_bu_lam_bu`
--

CREATE TABLE `nghi_bu_lam_bu` (
  `id` int(11) NOT NULL,
  `ma_don` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loai` enum('lam_truoc_nghi_sau','nghi_truoc_lam_sau') NOT NULL,
  `ngay_nghi` date NOT NULL,
  `buoi_nghi` enum('ca_ngay','buoi_sang','buoi_chieu') DEFAULT 'ca_ngay',
  `ngay_lam_bu` date DEFAULT NULL,
  `buoi_lam_bu` enum('ca_ngay','buoi_sang','buoi_chieu') DEFAULT 'ca_ngay',
  `so_ngay` decimal(4,1) NOT NULL,
  `ly_do` text DEFAULT NULL,
  `trang_thai` enum('cho_xac_nhan','dang_cho_bu','da_hoan_thanh','qua_han','tu_choi') DEFAULT 'cho_xac_nhan',
  `da_xac_nhan_hoan_thanh` tinyint(4) DEFAULT 0,
  `nguoi_xac_nhan` int(11) DEFAULT NULL,
  `ngay_xac_nhan` timestamp NULL DEFAULT NULL,
  `han_hoan_thanh` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `ma_nv` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_app_password` varchar(255) DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ') DEFAULT 'Nam',
  `ngay_sinh` date DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `khoa_phong_id` int(11) DEFAULT NULL,
  `vai_tro_id` int(11) NOT NULL,
  `ngay_bat_dau_lam` date NOT NULL,
  `so_ngay_phep_nam` int(11) DEFAULT 12,
  `so_ngay_phep_da_dung` decimal(4,1) DEFAULT 0.0,
  `so_ngay_phep_nam_truoc` decimal(4,1) DEFAULT 0.0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `ma_nv`, `username`, `password`, `ho_ten`, `email`, `email_app_password`, `gioi_tinh`, `ngay_sinh`, `so_dien_thoai`, `dia_chi`, `khoa_phong_id`, `vai_tro_id`, `ngay_bat_dau_lam`, `so_ngay_phep_nam`, `so_ngay_phep_da_dung`, `so_ngay_phep_nam_truoc`, `trang_thai`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin@school.edu.vn', NULL, 'Nam', NULL, NULL, NULL, NULL, 8, '2024-01-01', 12, 0.0, 0.0, 1, NULL, '2025-11-15 01:11:56', '2025-11-15 01:11:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_don_nghi_phep_chi_tiet`
-- (See below for the actual view)
--
CREATE TABLE `v_don_nghi_phep_chi_tiet` (
`id` int(11)
,`ma_don` varchar(50)
,`ma_nv` varchar(20)
,`ten_nhan_vien` varchar(255)
,`ten_khoa_phong` varchar(255)
,`loai_phep` varchar(100)
,`tinh_phep_nam` tinyint(4)
,`ngay_bat_dau` date
,`buoi_bat_dau` enum('ca_ngay','buoi_sang','buoi_chieu')
,`ngay_ket_thuc` date
,`buoi_ket_thuc` enum('ca_ngay','buoi_sang','buoi_chieu')
,`so_ngay_nghi` decimal(4,1)
,`ly_do` text
,`trang_thai` enum('cho_duyet','cap_don_vi_duyet','cap_pht_duyet','da_duyet','tu_choi')
,`ngay_tao` timestamp
,`nguoi_duyet_cap_don_vi` varchar(255)
,`ngay_duyet_cap_don_vi` timestamp
,`nguoi_duyet_cap_pht` varchar(255)
,`ngay_duyet_cap_pht` timestamp
,`nguoi_duyet_cuoi` varchar(255)
,`ngay_duyet_cuoi` timestamp
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_thong_ke_phep`
-- (See below for the actual view)
--
CREATE TABLE `v_thong_ke_phep` (
`id` int(11)
,`ma_nv` varchar(20)
,`ho_ten` varchar(255)
,`so_ngay_phep_nam` int(11)
,`so_ngay_phep_da_dung` decimal(4,1)
,`so_ngay_phep_con_lai` decimal(12,1)
,`so_ngay_phep_nam_truoc` decimal(4,1)
,`tong_so_don` bigint(21)
,`don_cho_duyet` bigint(21)
,`don_da_duyet` bigint(21)
,`don_tu_choi` bigint(21)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_don_nghi_phep_chi_tiet`
--
DROP TABLE IF EXISTS `v_don_nghi_phep_chi_tiet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_don_nghi_phep_chi_tiet`  AS SELECT `d`.`id` AS `id`, `d`.`ma_don` AS `ma_don`, `u`.`ma_nv` AS `ma_nv`, `u`.`ho_ten` AS `ten_nhan_vien`, `kp`.`ten_khoa_phong` AS `ten_khoa_phong`, `lp`.`ten_loai` AS `loai_phep`, `lp`.`tinh_phep_nam` AS `tinh_phep_nam`, `d`.`ngay_bat_dau` AS `ngay_bat_dau`, `d`.`buoi_bat_dau` AS `buoi_bat_dau`, `d`.`ngay_ket_thuc` AS `ngay_ket_thuc`, `d`.`buoi_ket_thuc` AS `buoi_ket_thuc`, `d`.`so_ngay_nghi` AS `so_ngay_nghi`, `d`.`ly_do` AS `ly_do`, `d`.`trang_thai` AS `trang_thai`, `d`.`created_at` AS `ngay_tao`, `u1`.`ho_ten` AS `nguoi_duyet_cap_don_vi`, `d`.`ngay_duyet_cap_don_vi` AS `ngay_duyet_cap_don_vi`, `u2`.`ho_ten` AS `nguoi_duyet_cap_pht`, `d`.`ngay_duyet_cap_pht` AS `ngay_duyet_cap_pht`, `u3`.`ho_ten` AS `nguoi_duyet_cuoi`, `d`.`ngay_duyet_cuoi` AS `ngay_duyet_cuoi` FROM ((((((`don_nghi_phep` `d` join `users` `u` on(`d`.`user_id` = `u`.`id`)) join `dm_khoa_phong` `kp` on(`u`.`khoa_phong_id` = `kp`.`id`)) join `dm_loai_phep` `lp` on(`d`.`loai_phep_id` = `lp`.`id`)) left join `users` `u1` on(`d`.`nguoi_duyet_cap_don_vi` = `u1`.`id`)) left join `users` `u2` on(`d`.`nguoi_duyet_cap_pht` = `u2`.`id`)) left join `users` `u3` on(`d`.`nguoi_duyet_cuoi` = `u3`.`id`)) ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_thong_ke_phep`
--
DROP TABLE IF EXISTS `v_thong_ke_phep`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_thong_ke_phep`  AS SELECT `u`.`id` AS `id`, `u`.`ma_nv` AS `ma_nv`, `u`.`ho_ten` AS `ho_ten`, `u`.`so_ngay_phep_nam` AS `so_ngay_phep_nam`, `u`.`so_ngay_phep_da_dung` AS `so_ngay_phep_da_dung`, `u`.`so_ngay_phep_nam`- `u`.`so_ngay_phep_da_dung` AS `so_ngay_phep_con_lai`, `u`.`so_ngay_phep_nam_truoc` AS `so_ngay_phep_nam_truoc`, count(distinct `d`.`id`) AS `tong_so_don`, count(distinct case when `d`.`trang_thai` = 'cho_duyet' then `d`.`id` end) AS `don_cho_duyet`, count(distinct case when `d`.`trang_thai` = 'da_duyet' then `d`.`id` end) AS `don_da_duyet`, count(distinct case when `d`.`trang_thai` = 'tu_choi' then `d`.`id` end) AS `don_tu_choi` FROM (`users` `u` left join `don_nghi_phep` `d` on(`u`.`id` = `d`.`user_id`)) GROUP BY `u`.`id` ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bao_nghi_bao_bu`
--
ALTER TABLE `bao_nghi_bao_bu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_bao` (`ma_bao`),
  ADD KEY `lop_hoc_id` (`lop_hoc_id`),
  ADD KEY `mon_hoc_id` (`mon_hoc_id`),
  ADD KEY `nguoi_duyet` (`nguoi_duyet`),
  ADD KEY `idx_giang_vien` (`giang_vien_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `cau_hinh_he_thong`
--
ALTER TABLE `cau_hinh_he_thong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_cau_hinh` (`ten_cau_hinh`);

--
-- Chỉ mục cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cham_cong` (`user_id`,`ngay_cham`),
  ADD KEY `loai_phep_id` (`loai_phep_id`),
  ADD KEY `nguoi_cham` (`nguoi_cham`),
  ADD KEY `idx_user_ngay` (`user_id`,`ngay_cham`);

--
-- Chỉ mục cho bảng `di_tre`
--
ALTER TABLE `di_tre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_ghi_nhan` (`nguoi_ghi_nhan`),
  ADD KEY `idx_user_ngay` (`user_id`,`ngay_di_tre`);

--
-- Chỉ mục cho bảng `dm_khoa_phong`
--
ALTER TABLE `dm_khoa_phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_khoa_phong` (`ma_khoa_phong`);

--
-- Chỉ mục cho bảng `dm_loai_phep`
--
ALTER TABLE `dm_loai_phep`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_loai` (`ma_loai`);

--
-- Chỉ mục cho bảng `dm_lop_hoc`
--
ALTER TABLE `dm_lop_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_lop` (`ma_lop`),
  ADD KEY `khoa_phong_id` (`khoa_phong_id`);

--
-- Chỉ mục cho bảng `dm_mon_hoc`
--
ALTER TABLE `dm_mon_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_mon` (`ma_mon`);

--
-- Chỉ mục cho bảng `dm_vai_tro`
--
ALTER TABLE `dm_vai_tro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_vai_tro` (`ma_vai_tro`);

--
-- Chỉ mục cho bảng `don_nghi_phep`
--
ALTER TABLE `don_nghi_phep`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_don` (`ma_don`),
  ADD KEY `loai_phep_id` (`loai_phep_id`),
  ADD KEY `nguoi_duyet_cap_don_vi` (`nguoi_duyet_cap_don_vi`),
  ADD KEY `nguoi_duyet_cap_pht` (`nguoi_duyet_cap_pht`),
  ADD KEY `nguoi_duyet_cuoi` (`nguoi_duyet_cuoi`),
  ADD KEY `idx_ma_don` (`ma_don`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`),
  ADD KEY `idx_ngay_bat_dau` (`ngay_bat_dau`);

--
-- Chỉ mục cho bảng `hop_dong_thinh_giang`
--
ALTER TABLE `hop_dong_thinh_giang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_hop_dong` (`ma_hop_dong`),
  ADD KEY `giang_vien_id` (`giang_vien_id`),
  ADD KEY `mon_hoc_id` (`mon_hoc_id`),
  ADD KEY `nguoi_tao` (`nguoi_tao`);

--
-- Chỉ mục cho bảng `lich_su_mail`
--
ALTER TABLE `lich_su_mail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_gui` (`nguoi_gui`),
  ADD KEY `idx_trang_thai` (`trang_thai`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `nghi_bu_lam_bu`
--
ALTER TABLE `nghi_bu_lam_bu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_don` (`ma_don`),
  ADD KEY `nguoi_xac_nhan` (`nguoi_xac_nhan`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`);

--
-- Chỉ mục cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_nv` (`ma_nv`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `khoa_phong_id` (`khoa_phong_id`),
  ADD KEY `vai_tro_id` (`vai_tro_id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_ma_nv` (`ma_nv`),
  ADD KEY `idx_email` (`email`);

--
-- Chỉ mục cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bao_nghi_bao_bu`
--
ALTER TABLE `bao_nghi_bao_bu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cau_hinh_he_thong`
--
ALTER TABLE `cau_hinh_he_thong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `di_tre`
--
ALTER TABLE `di_tre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dm_khoa_phong`
--
ALTER TABLE `dm_khoa_phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dm_loai_phep`
--
ALTER TABLE `dm_loai_phep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `dm_lop_hoc`
--
ALTER TABLE `dm_lop_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dm_mon_hoc`
--
ALTER TABLE `dm_mon_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dm_vai_tro`
--
ALTER TABLE `dm_vai_tro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `don_nghi_phep`
--
ALTER TABLE `don_nghi_phep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hop_dong_thinh_giang`
--
ALTER TABLE `hop_dong_thinh_giang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lich_su_mail`
--
ALTER TABLE `lich_su_mail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nghi_bu_lam_bu`
--
ALTER TABLE `nghi_bu_lam_bu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bao_nghi_bao_bu`
--
ALTER TABLE `bao_nghi_bao_bu`
  ADD CONSTRAINT `bao_nghi_bao_bu_ibfk_1` FOREIGN KEY (`giang_vien_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bao_nghi_bao_bu_ibfk_2` FOREIGN KEY (`lop_hoc_id`) REFERENCES `dm_lop_hoc` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bao_nghi_bao_bu_ibfk_3` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dm_mon_hoc` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bao_nghi_bao_bu_ibfk_4` FOREIGN KEY (`nguoi_duyet`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD CONSTRAINT `cham_cong_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cham_cong_ibfk_2` FOREIGN KEY (`loai_phep_id`) REFERENCES `dm_loai_phep` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cham_cong_ibfk_3` FOREIGN KEY (`nguoi_cham`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `di_tre`
--
ALTER TABLE `di_tre`
  ADD CONSTRAINT `di_tre_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `di_tre_ibfk_2` FOREIGN KEY (`nguoi_ghi_nhan`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `dm_lop_hoc`
--
ALTER TABLE `dm_lop_hoc`
  ADD CONSTRAINT `dm_lop_hoc_ibfk_1` FOREIGN KEY (`khoa_phong_id`) REFERENCES `dm_khoa_phong` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `don_nghi_phep`
--
ALTER TABLE `don_nghi_phep`
  ADD CONSTRAINT `don_nghi_phep_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `don_nghi_phep_ibfk_2` FOREIGN KEY (`loai_phep_id`) REFERENCES `dm_loai_phep` (`id`),
  ADD CONSTRAINT `don_nghi_phep_ibfk_3` FOREIGN KEY (`nguoi_duyet_cap_don_vi`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_nghi_phep_ibfk_4` FOREIGN KEY (`nguoi_duyet_cap_pht`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_nghi_phep_ibfk_5` FOREIGN KEY (`nguoi_duyet_cuoi`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hop_dong_thinh_giang`
--
ALTER TABLE `hop_dong_thinh_giang`
  ADD CONSTRAINT `hop_dong_thinh_giang_ibfk_1` FOREIGN KEY (`giang_vien_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_thinh_giang_ibfk_2` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dm_mon_hoc` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_thinh_giang_ibfk_3` FOREIGN KEY (`nguoi_tao`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `lich_su_mail`
--
ALTER TABLE `lich_su_mail`
  ADD CONSTRAINT `lich_su_mail_ibfk_1` FOREIGN KEY (`nguoi_gui`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `nghi_bu_lam_bu`
--
ALTER TABLE `nghi_bu_lam_bu`
  ADD CONSTRAINT `nghi_bu_lam_bu_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nghi_bu_lam_bu_ibfk_2` FOREIGN KEY (`nguoi_xac_nhan`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`khoa_phong_id`) REFERENCES `dm_khoa_phong` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`vai_tro_id`) REFERENCES `dm_vai_tro` (`id`);

--
-- Các ràng buộc cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Cơ sở dữ liệu: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

--
-- Đang đổ dữ liệu cho bảng `pma__export_templates`
--

INSERT INTO `pma__export_templates` (`id`, `username`, `export_type`, `template_name`, `template_data`) VALUES
(1, 'root', 'database', 'appnghiphep', '{\"quick_or_custom\":\"quick\",\"what\":\"sql\",\"structure_or_data_forced\":\"0\",\"table_select[]\":[\"cauhinhemail\",\"donnghiphep\",\"lichdaybu\",\"lichsuemail\",\"nguoidung\",\"vaitro\"],\"table_structure[]\":[\"cauhinhemail\",\"donnghiphep\",\"lichdaybu\",\"lichsuemail\",\"nguoidung\",\"vaitro\"],\"table_data[]\":[\"cauhinhemail\",\"donnghiphep\",\"lichdaybu\",\"lichsuemail\",\"nguoidung\",\"vaitro\"],\"aliases_new\":\"\",\"output_format\":\"sendit\",\"filename_template\":\"@DATABASE@\",\"remember_template\":\"on\",\"charset\":\"utf-8\",\"compression\":\"none\",\"maxsize\":\"\",\"codegen_structure_or_data\":\"data\",\"codegen_format\":\"0\",\"csv_separator\":\",\",\"csv_enclosed\":\"\\\"\",\"csv_escaped\":\"\\\"\",\"csv_terminated\":\"AUTO\",\"csv_null\":\"NULL\",\"csv_columns\":\"something\",\"csv_structure_or_data\":\"data\",\"excel_null\":\"NULL\",\"excel_columns\":\"something\",\"excel_edition\":\"win\",\"excel_structure_or_data\":\"data\",\"json_structure_or_data\":\"data\",\"json_unicode\":\"something\",\"latex_caption\":\"something\",\"latex_structure_or_data\":\"structure_and_data\",\"latex_structure_caption\":\"Cấu trúc của bảng @TABLE@\",\"latex_structure_continued_caption\":\"Cấu trúc của bảng @TABLE@ (còn nữa)\",\"latex_structure_label\":\"tab:@TABLE@-structure\",\"latex_relation\":\"something\",\"latex_comments\":\"something\",\"latex_mime\":\"something\",\"latex_columns\":\"something\",\"latex_data_caption\":\"Nội dung của bảng @TABLE@\",\"latex_data_continued_caption\":\"Nội dung của bảng @TABLE@ (còn nữa)\",\"latex_data_label\":\"tab:@TABLE@-data\",\"latex_null\":\"\\\\textit{NULL}\",\"mediawiki_structure_or_data\":\"structure_and_data\",\"mediawiki_caption\":\"something\",\"mediawiki_headers\":\"something\",\"htmlword_structure_or_data\":\"structure_and_data\",\"htmlword_null\":\"NULL\",\"ods_null\":\"NULL\",\"ods_structure_or_data\":\"data\",\"odt_structure_or_data\":\"structure_and_data\",\"odt_relation\":\"something\",\"odt_comments\":\"something\",\"odt_mime\":\"something\",\"odt_columns\":\"something\",\"odt_null\":\"NULL\",\"pdf_report_title\":\"\",\"pdf_structure_or_data\":\"structure_and_data\",\"phparray_structure_or_data\":\"data\",\"sql_include_comments\":\"something\",\"sql_header_comment\":\"\",\"sql_use_transaction\":\"something\",\"sql_compatibility\":\"NONE\",\"sql_structure_or_data\":\"structure_and_data\",\"sql_create_table\":\"something\",\"sql_auto_increment\":\"something\",\"sql_create_view\":\"something\",\"sql_procedure_function\":\"something\",\"sql_create_trigger\":\"something\",\"sql_backquotes\":\"something\",\"sql_type\":\"INSERT\",\"sql_insert_syntax\":\"both\",\"sql_max_query_size\":\"50000\",\"sql_hex_for_binary\":\"something\",\"sql_utc_time\":\"something\",\"texytext_structure_or_data\":\"structure_and_data\",\"texytext_null\":\"NULL\",\"xml_structure_or_data\":\"data\",\"xml_export_events\":\"something\",\"xml_export_functions\":\"something\",\"xml_export_procedures\":\"something\",\"xml_export_tables\":\"something\",\"xml_export_triggers\":\"something\",\"xml_export_views\":\"something\",\"xml_export_contents\":\"something\",\"yaml_structure_or_data\":\"data\",\"\":null,\"lock_tables\":null,\"as_separate_files\":null,\"csv_removeCRLF\":null,\"excel_removeCRLF\":null,\"json_pretty_print\":null,\"htmlword_columns\":null,\"ods_columns\":null,\"sql_dates\":null,\"sql_relation\":null,\"sql_mime\":null,\"sql_disable_fk\":null,\"sql_views_as_tables\":null,\"sql_metadata\":null,\"sql_create_database\":null,\"sql_drop_table\":null,\"sql_if_not_exists\":null,\"sql_simple_view_export\":null,\"sql_view_current_user\":null,\"sql_or_replace_view\":null,\"sql_truncate\":null,\"sql_delayed\":null,\"sql_ignore\":null,\"texytext_columns\":null}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Đang đổ dữ liệu cho bảng `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"leave_app\",\"table\":\"employees\"},{\"db\":\"appnghiphep\",\"table\":\"DonNghiPhep\"},{\"db\":\"appnghiphep\",\"table\":\"nguoidung\"},{\"db\":\"appnghiphep\",\"table\":\"vaitro\"},{\"db\":\"appnghiphep\",\"table\":\"NghiBu\"},{\"db\":\"appnghiphep\",\"table\":\"NguoiDung\"},{\"db\":\"leave_management\",\"table\":\"email_settings\"},{\"db\":\"appnghiphep\",\"table\":\"cauhinhemail\"},{\"db\":\"\",\"table\":\"NGUOIDUNG\"},{\"db\":\"leave_management\",\"table\":\"employees\"}]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Đang đổ dữ liệu cho bảng `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-11-14 23:59:24', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"vi\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Chỉ mục cho bảng `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Chỉ mục cho bảng `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Chỉ mục cho bảng `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Chỉ mục cho bảng `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Chỉ mục cho bảng `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Chỉ mục cho bảng `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Chỉ mục cho bảng `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Chỉ mục cho bảng `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Chỉ mục cho bảng `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Chỉ mục cho bảng `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Cơ sở dữ liệu: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Cơ sở dữ liệu: `_sms`
--
CREATE DATABASE IF NOT EXISTS `_sms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `_sms`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `s_no` int(20) NOT NULL,
  `id` varchar(30) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `dob` varchar(20) NOT NULL,
  `image` varchar(40) NOT NULL DEFAULT '1701517055user.png',
  `phone` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `address` varchar(700) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`s_no`, `id`, `fname`, `lname`, `dob`, `image`, `phone`, `gender`, `address`) VALUES
(2, 'A9876543210', 'Admin', 'Kumar', '12/11/2024', 'A98765432101718792069.png', '1234567890', 'male', 'no where');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendence`
--

CREATE TABLE `attendence` (
  `s_no` int(20) NOT NULL,
  `student_id` varchar(40) NOT NULL,
  `attendence` varchar(10) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(5) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendence`
--

INSERT INTO `attendence` (`s_no`, `student_id`, `attendence`, `class`, `section`, `date`) VALUES
(1, 'S1718791292', '1', '12c', 'A', '2024-06-19 15:32:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `buses`
--

CREATE TABLE `buses` (
  `s_no` int(11) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `bus_title` varchar(100) NOT NULL,
  `bus_number` varchar(20) NOT NULL,
  `request` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `buses`
--

INSERT INTO `buses` (`s_no`, `bus_id`, `bus_title`, `bus_number`, `request`) VALUES
(10, '1718791847', 'Bus 1', 'XXXXXX', ''),
(11, '1718791949', 'Bus 2', '999999', ''),
(12, '1718791984', 'Another bus', 'OOOOOOOO', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bus_root`
--

CREATE TABLE `bus_root` (
  `s_no` int(11) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `arrival_time` varchar(20) NOT NULL,
  `serial` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bus_root`
--

INSERT INTO `bus_root` (`s_no`, `bus_id`, `location`, `arrival_time`, `serial`) VALUES
(7, '1718791847', 'Stop 1', '06:50 AM', 1),
(8, '1718791847', 'Stop 2', '07:00 AM', 2),
(9, '1718791847', 'Stop 3', '07:10 AM', 3),
(10, '1718791847', 'SCHOOL', '10:00 AM', 4),
(13, '1718791949', 'Stop z', '06:35 AM', 1),
(14, '1718791949', 'SCHOOL', '10:00 AM', 2),
(15, '1718791984', 'Stop x', '06:45 AM', 1),
(16, '1718791984', 'SCHOOL', '10:00 AM', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bus_staff`
--

CREATE TABLE `bus_staff` (
  `s_no` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `bus_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bus_staff`
--

INSERT INTO `bus_staff` (`s_no`, `id`, `bus_id`, `name`, `contact`, `role`) VALUES
(1, 'B1718791847', '1718791847', 'driver ', '8080808080', 'driver'),
(2, 'B1718791847', '1718791847', 'helper ', '0000000000', 'helper'),
(3, 'B1718791949', '1718791949', 'driver 2', '7897898988', 'driver'),
(4, 'B1718791949', '1718791949', 'helper', '7897898988', 'helper'),
(5, 'B1718791984', '1718791984', 'another driver', '7897897898', 'driver'),
(6, 'B1718791984', '1718791984', 'another helper', '7894568796', 'helper');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `s_no` int(10) NOT NULL,
  `class` varchar(20) NOT NULL,
  `section` varchar(1) NOT NULL,
  `fees` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `s_no` int(20) NOT NULL,
  `exam_id` varchar(40) NOT NULL,
  `exam_title` varchar(512) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(10) NOT NULL,
  `total_marks` varchar(10) NOT NULL,
  `passing_marks` varchar(10) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exams`
--

INSERT INTO `exams` (`s_no`, `exam_id`, `exam_title`, `subject`, `class`, `section`, `total_marks`, `passing_marks`, `timestamp`) VALUES
(3, 'E17187917486672ae442b976', 'Monthly test ', 'ALL', '12c', 'A', '100', '33', '2024-06-19 15:39:08'),
(4, 'E17187928006672b26095672', 'Hindi exam result', 'Hindi', '12c', 'A', '100', '33', '2024-06-19 15:56:40'),
(5, 'E17187929656672b305cbb25', 'sldfj', 'ALL', '12c', 'A', '100', '33', '2024-06-19 15:59:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `feedback`
--

CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL,
  `sender_id` varchar(20) NOT NULL,
  `receiver_id` varchar(20) NOT NULL,
  `msg` varchar(500) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `feedback`
--

INSERT INTO `feedback` (`s_no`, `sender_id`, `receiver_id`, `msg`, `timestamp`) VALUES
(7, 'T1718791191', 'S1718791292', 'Hello student', '2024-06-19 15:46:58'),
(8, 'T1718791191', 'S1718791292', 'You are so naughty\n', '2024-06-19 15:47:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fee_record`
--

CREATE TABLE `fee_record` (
  `s_no` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `month` varchar(20) NOT NULL,
  `other_collection` int(5) NOT NULL,
  `total` int(5) NOT NULL,
  `paid` int(5) NOT NULL,
  `balance` int(5) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leaves`
--

CREATE TABLE `leaves` (
  `s_no` int(10) NOT NULL,
  `sender_id` varchar(20) NOT NULL,
  `send_date` datetime NOT NULL DEFAULT current_timestamp(),
  `leave_type` varchar(100) NOT NULL,
  `leave_desc` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `leaves`
--

INSERT INTO `leaves` (`s_no`, `sender_id`, `send_date`, `leave_type`, `leave_desc`, `start_date`, `end_date`, `status`) VALUES
(12, 'T1718791191', '2024-06-19 15:48:43', 'Medical Leave', 'accept my leave otherwise ....... ', '2024-06-20 00:00:00', '2024-06-27 00:00:00', 'rejected'),
(13, 'T1718791191', '2024-06-19 15:49:23', 'Casual Leave', 'I want some rest please give me leave', '2024-06-29 00:00:00', '2024-07-03 00:00:00', 'rejected'),
(14, 'T1718791191', '2025-10-31 07:55:32', 'Casual Leave', 'asdasd', '2025-11-01 00:00:00', '2025-11-05 00:00:00', 'pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `marks`
--

CREATE TABLE `marks` (
  `s_no` int(20) NOT NULL,
  `exam_id` varchar(40) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `student_id` varchar(40) NOT NULL,
  `marks` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `marks`
--

INSERT INTO `marks` (`s_no`, `exam_id`, `subject`, `student_id`, `marks`) VALUES
(1, 'E17187917486672ae442b976', 'Hindi', 'S1718791292', '86'),
(2, 'E17187917486672ae442b976', 'Commerce', 'S1718791292', '62'),
(3, 'E17187917486672ae442b976', 'English', 'S1718791292', '59'),
(4, 'E17187928006672b26095672', 'Hindi', 'S1718791292', '33'),
(5, 'E17187929656672b305cbb25', 'Hindi', 'S1718791292', '55'),
(6, 'E17187929656672b305cbb25', 'Commerce', 'S1718791292', '55'),
(7, 'E17187929656672b305cbb25', 'English', 'S1718791292', '21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notes`
--

CREATE TABLE `notes` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `class` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `file` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notes`
--

INSERT INTO `notes` (`s_no`, `sender_id`, `editor_id`, `class`, `subject`, `title`, `comment`, `file`, `timestamp`) VALUES
(1, 'A9876543210', 'A9876543210', '12c', 'Hindi', 'Hindi Homework ', 'do this on time', 'A98765432101718791715.png', '2024-06-19 15:38:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notice`
--

CREATE TABLE `notice` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` varchar(1000) NOT NULL,
  `file` varchar(100) NOT NULL,
  `importance` varchar(5) NOT NULL DEFAULT '1',
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(11) NOT NULL,
  `class` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notice`
--

INSERT INTO `notice` (`s_no`, `sender_id`, `editor_id`, `title`, `body`, `file`, `importance`, `timestamp`, `role`, `class`) VALUES
(51, 'A9876543210', 'A9876543210', 'Notice title', 'body', 'A98765432101718791385.png', '2', '2024-06-19 15:33:05', '', ''),
(52, 'A9876543210', 'A9876543210', 'Title 2', 'body 2', 'A98765432101718791411.png', '3', '2024-06-19 15:33:31', '', ''),
(53, 'A9876543210', 'A9876543210', 'Holiday notice', 'enjoy your holidays', 'A98765432101718791447.png', '1', '2024-06-19 15:34:07', '', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `s_no` int(20) NOT NULL,
  `sender_id` varchar(40) NOT NULL,
  `class` varchar(30) NOT NULL,
  `section` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `file` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payroll`
--

CREATE TABLE `payroll` (
  `s_no` int(11) NOT NULL,
  `id` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  `date` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account_no` varchar(40) NOT NULL,
  `ifsc_code` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reminders`
--

CREATE TABLE `reminders` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `reminders`
--

INSERT INTO `reminders` (`s_no`, `id`, `message`, `status`) VALUES
(68, 'T1718791191', 'Post a homework daily', 'completed'),
(69, 'T1718791191', 'principal meeting', 'pending'),
(70, 'A9876543210', 'Reminder for myself : have a good day', 'pending'),
(71, 'A9876543210', '\nBest of luck', 'completed');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `students`
--

CREATE TABLE `students` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `father` varchar(200) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `class` varchar(20) NOT NULL,
  `section` varchar(50) NOT NULL,
  `dob` varchar(15) NOT NULL,
  `image` varchar(50) NOT NULL DEFAULT '1701517055user.png',
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `state` varchar(50) NOT NULL,
  `request_date` varchar(30) NOT NULL,
  `request_time` varchar(30) NOT NULL,
  `request` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `students`
--

INSERT INTO `students` (`s_no`, `id`, `fname`, `lname`, `father`, `gender`, `class`, `section`, `dob`, `image`, `phone`, `email`, `address`, `city`, `zip`, `state`, `request_date`, `request_time`, `request`) VALUES
(1, 'S1718791292', 'Student', 'kumar', 'father G', 'Male', '12c', 'A', '19-06-2024', 'S17187912921718791292.png', '7894561230', 'student@gmail.com', 'near teachers house', 'home town', '789654', 'Panjab', '', '', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `student_guardian`
--

CREATE TABLE `student_guardian` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `gname` varchar(200) NOT NULL,
  `gphone` varchar(20) NOT NULL,
  `gaddress` varchar(200) NOT NULL,
  `gcity` varchar(100) NOT NULL,
  `gzip` varchar(50) NOT NULL,
  `relation` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `student_guardian`
--

INSERT INTO `student_guardian` (`s_no`, `id`, `gname`, `gphone`, `gaddress`, `gcity`, `gzip`, `relation`) VALUES
(1, 'S1718791292', 'Regan Clemons', '4567894562', 'Eum sit et laboriosa', 'Abbot', 'Hunter', 'Culpa odio laboriosa');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subjects`
--

CREATE TABLE `subjects` (
  `s_no` int(20) NOT NULL,
  `subject_id` varchar(40) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `subjects`
--

INSERT INTO `subjects` (`s_no`, `subject_id`, `subject_name`, `class`) VALUES
(1, '12c6672ac911a253', 'Hindi', '12c'),
(2, '12c6672ac9c45d68', 'Commerce', '12c'),
(3, '12c6672aca78c3c7', 'English', '12c');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `syllabus`
--

CREATE TABLE `syllabus` (
  `s_no` int(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `syllabus`
--

INSERT INTO `syllabus` (`s_no`, `class`, `subject`, `file`) VALUES
(12, '12c', 'Hindi', 'T17187911911718792274.png'),
(13, '12c', 'English', 'T17187911911718792285.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teachers`
--

CREATE TABLE `teachers` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `father` varchar(150) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` varchar(20) NOT NULL,
  `image` varchar(30) NOT NULL DEFAULT '1701517055user.png',
  `phone` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(512) NOT NULL,
  `city` varchar(50) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `state` varchar(50) NOT NULL,
  `class` varchar(20) NOT NULL,
  `section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `teachers`
--

INSERT INTO `teachers` (`s_no`, `id`, `fname`, `lname`, `father`, `subject`, `gender`, `dob`, `image`, `phone`, `email`, `address`, `city`, `zip`, `state`, `class`, `section`) VALUES
(1, 'T1718791191', 'teacher', 'kumar', '', 'Hindi', 'Male', '19-06-2024', 'T17187911911718792416.png', '7896541230', 'teacher@gmail.com', 'near admins house', 'home town', '478548', 'Delhi', '12c', 'A'),
(2, 'T1761872060', 'Roger', 'Cao', '', 'Plague Glimmer Large Cluster Jewel', 'Male', '01-01-1978', '1701517055user.png', '1234567890', 'roger@gmail.com', 'abcdef', 'TPHCM', '123456', 'Hariyana', '12c', 'A');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teacher_guardian`
--

CREATE TABLE `teacher_guardian` (
  `s_no` int(20) NOT NULL,
  `id` varchar(40) NOT NULL,
  `gname` varchar(256) NOT NULL,
  `gphone` varchar(20) NOT NULL,
  `gaddress` varchar(256) NOT NULL,
  `gcity` varchar(50) NOT NULL,
  `gzip` varchar(20) NOT NULL,
  `relation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `teacher_guardian`
--

INSERT INTO `teacher_guardian` (`s_no`, `id`, `gname`, `gphone`, `gaddress`, `gcity`, `gzip`, `relation`) VALUES
(1, 'T1718791191', 'Velma Walker', '1234567895', 'Sit voluptas nisi v', 'Maggie', 'Mckee', 'Consequatur Volupta'),
(2, 'T1761872060', 'adasd', '0123456987', 'dasfasf', 'safasfa', '23235', 'Abc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `time_table`
--

CREATE TABLE `time_table` (
  `s_no` int(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `section` varchar(10) NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `mon` varchar(30) NOT NULL,
  `tue` varchar(30) NOT NULL,
  `wed` varchar(30) NOT NULL,
  `thu` varchar(30) NOT NULL,
  `fri` varchar(30) NOT NULL,
  `sat` varchar(30) NOT NULL,
  `editor_id` varchar(40) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `time_table`
--

INSERT INTO `time_table` (`s_no`, `class`, `section`, `start_time`, `end_time`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `editor_id`, `timestamp`) VALUES
(1, '12c', 'A', '07:00', '08:00', 'Hindi', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(2, '12c', 'A', '08:00', '09:00', 'English', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(3, '12c', 'A', '09:00', '10:00', 'Math', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(4, '12c', 'A', '10:00', '11:00', 'Hindi', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(5, '12c', 'A', '11:00', '12:00', 'English', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(6, '12c', 'A', '12:00', '01:00', 'Commerce', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(7, '12c', 'A', '01:00', '02:00', 'Commerce', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37'),
(8, '12c', 'A', '02:00', '03:00', 'Hindi', '', '', '', '', '', 'A9876543210', '2024-06-19 15:37:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `s_no` int(15) NOT NULL,
  `id` varchar(40) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password_hash` varchar(700) NOT NULL,
  `role` varchar(20) NOT NULL,
  `theme` varchar(20) NOT NULL DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`s_no`, `id`, `email`, `password_hash`, `role`, `theme`) VALUES
(1, 'A9876543210', 'admin@gmail.com', '$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe', 'admin', 'light'),
(2, 'T1718791191', 'teacher@gmail.com', '$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe', 'teacher', 'light'),
(3, 'S1718791292', 'student@gmail.com', '$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe', 'student', 'light'),
(4, 'O7898987845', 'owner@gmail.com', '$2y$10$2MrhbQa30mll8mKG6LPyjuI7CQPC4abCvqrSvczxXVRu4RVueRfoe', 'owner', 'light'),
(5, 'T1761872060', 'roger@gmail.com', '$2y$10$x55vtI8N1IxMJHX1SQlx8uUOV4Uf4HEfHfglsDEs31Ggr3okDO8ky', 'teacher', 'light');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `attendence`
--
ALTER TABLE `attendence`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `bus_root`
--
ALTER TABLE `bus_root`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `bus_staff`
--
ALTER TABLE `bus_staff`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `fee_record`
--
ALTER TABLE `fee_record`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `student_guardian`
--
ALTER TABLE `student_guardian`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `syllabus`
--
ALTER TABLE `syllabus`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `teacher_guardian`
--
ALTER TABLE `teacher_guardian`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `time_table`
--
ALTER TABLE `time_table`
  ADD PRIMARY KEY (`s_no`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`s_no`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `attendence`
--
ALTER TABLE `attendence`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `buses`
--
ALTER TABLE `buses`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `bus_root`
--
ALTER TABLE `bus_root`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `bus_staff`
--
ALTER TABLE `bus_staff`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `s_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `exams`
--
ALTER TABLE `exams`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `feedback`
--
ALTER TABLE `feedback`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `fee_record`
--
ALTER TABLE `fee_record`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `leaves`
--
ALTER TABLE `leaves`
  MODIFY `s_no` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `marks`
--
ALTER TABLE `marks`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `notes`
--
ALTER TABLE `notes`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `notice`
--
ALTER TABLE `notice`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payroll`
--
ALTER TABLE `payroll`
  MODIFY `s_no` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reminders`
--
ALTER TABLE `reminders`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT cho bảng `students`
--
ALTER TABLE `students`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `student_guardian`
--
ALTER TABLE `student_guardian`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `subjects`
--
ALTER TABLE `subjects`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `syllabus`
--
ALTER TABLE `syllabus`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `teachers`
--
ALTER TABLE `teachers`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `teacher_guardian`
--
ALTER TABLE `teacher_guardian`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `time_table`
--
ALTER TABLE `time_table`
  MODIFY `s_no` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `s_no` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
