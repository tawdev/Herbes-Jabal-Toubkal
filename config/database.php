<?php
// إعدادات الاتصال بقاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tawabil_db');

// إنشاء الاتصال
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8mb4");
        
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("خطأ في الاتصال: " . $e->getMessage());
    }
}

// إغلاق الاتصال
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>

