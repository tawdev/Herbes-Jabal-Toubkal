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
        
        // فحص خطأ الاتصال أولاً
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }
        
        // إجبار استخدام UTF-8mb4 للاتصال (يدعم جميع الأحرف بما فيها emoji)
        if (!$conn->set_charset("utf8mb4")) {
            // إذا فشل utf8mb4، جرب utf8
            if (!$conn->set_charset("utf8")) {
                // إذا فشل كلاهما، استخدم الإعداد الافتراضي
                error_log("تحذير: فشل تعيين charset إلى utf8mb4 أو utf8");
            }
        }
        
        // تعيين collation للاتصال لضمان التعامل الصحيح مع الأحرف العربية
        $conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        $conn->query("SET CHARACTER SET utf8mb4");
        $conn->query("SET character_set_connection=utf8mb4");
        $conn->query("SET character_set_results=utf8mb4");
        $conn->query("SET collation_connection='utf8mb4_unicode_ci'");
        
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

