1. Admin – user:admin
   Thiếu chức năng:
   Thống kê; cấu hình hệ thống.
   Quản lý người dùng lỗi:
   Fatal error: Uncaught PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'TrangThai' in 'where clause' in D:\xampp\htdocs\appnghiphep_v2\includes\functions.php:140 Stack trace: #0 D:\xampp\htdocs\appnghiphep_v2\includes\functions.php(140): PDO->query('\r\n SELEC...') #1 D:\xampp\htdocs\appnghiphep_v2\views\admin\nguoi_dung.php(163): getRoles(Object(PDO)) #2 {main} thrown in D:\xampp\htdocs\appnghiphep_v2\includes\functions.php on line 140

2. Giảng viên – user: giang_vien_1
   Thiếu chức năng:
   Tạo đơn nghỉ - dạy bù; đơn của tôi.

3. Hiệu trưởng – user: hieu_truong_1
   Thiếu chức năng:
   Thống kê; Duyệt đơn.
   Trong trang chủ:
   Không vào được cấu hình hệ thống; thống kê – báo cáo.
   Quản lý người dùng báo lỗi:
   Warning: Cannot modify header information - headers already sent by (output started at D:\xampp\htdocs\appnghiphep_v2\views\layouts\header.php:19) in D:\xampp\htdocs\appnghiphep_v2\includes\session.php on line 26

4. Nhân viên – user: nhan_vien_1
   Thiếu chức năng:
   Tạo đơn nghỉ bù – làm bù

5. Phó hiệu trưởng – user: pho_ht_1
   Thiếu chức năng:
   Thống kê
   Trong trang chủ: không vào được chức năng thống kê tổng quan.

6. Trưởng phòng – user: truong_phong_cntt
   Thiếu chức năng:
   Thống kê; Chấm công; Tạo đơn cho nhân viên.
   Trong trang chủ: không vào được thống kê báo cáo; chấm công; tạo đơn cho nhân viên.

Ngoài ra còn chức năng in đơn nghỉ phép chưa test được, chức năng in thống kê báo cáo chưa test do còn thiếu chức năng chính thống kê – báo cáo
