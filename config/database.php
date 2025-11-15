<?php
// config/database.php - Kết nối database với xử lý lỗi tốt hơn

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'appnghiphep_v2');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        // Log lỗi vào file
        error_log("Database Connection Error: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_error.log');
        die("⚠️ Không thể kết nối database. Vui lòng kiểm tra cấu hình!");
    }
}

// Kiểm tra kết nối khi file được include
try {
    $testConnection = getDBConnection();
} catch (Exception $e) {
    // Nếu lỗi sẽ die() ở trên
}
?>