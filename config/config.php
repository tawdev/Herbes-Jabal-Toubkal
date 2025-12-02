<?php
// إعدادات الترميز - يجب أن يكون في بداية الملف
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// إعدادات عامة للموقع
session_start();

// إعدادات اللغة
define('SITE_NAME', 'Tawabil - التوابل والبهارات');
define('CURRENCY', 'درهم');
// التأكد من أن رمز العملة صحيح وليس به أي ترميز خاطئ
define('CURRENCY_SYMBOL', 'DH'); // DH فقط، بدون أي أرقام أو رموز إضافية

// مسارات الملفات
// يمكنك ضبط هذا المتغير يدوياً إذا كان الموقع في مجلد فرعي معين على الاستضافة
// مثال: $manualBasePath = '/tawabil/';
$manualBasePath = ''; // اتركه فارغاً إذا كان الموقع في جذر الدومين

// تحديد البروتوكول والدومين والمسار تلقائياً
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

// إذا تم تحديد مسار يدوي نستخدمه، وإلا نستخدم المسار المكتشف تلقائياً
$basePath = $manualBasePath !== '' ? $manualBasePath : $scriptDir;
$basePath = '/' . trim($basePath, '/') . '/';
if ($basePath === '//') {
    $basePath = '/';
}

define('BASE_URL', $protocol . $host . $basePath);
define('UPLOAD_DIR', 'uploads/');
define('PRODUCT_IMAGES', 'images/products/');
define('RECIPE_IMAGES', 'images/recipes/');

// إعدادات الجلسة
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// دالة لإعادة التوجيه
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// دالة لتنسيق السعر
function formatPrice($price) {
    // تحويل السعر إلى نص أولاً لتنظيفه
    $priceStr = (string)$price;
    
    // إزالة أي أرقام غريبة تظهر قبل السعر (مثل 262145)
    $priceStr = preg_replace('/^\d+\s+/', '', $priceStr); // إزالة الأرقام في البداية
    $priceStr = preg_replace('/\s+\d+/', '', $priceStr); // إزالة الأرقام في النهاية
    $priceStr = preg_replace('/\d{6,}/', '', $priceStr); // إزالة أي أرقام طويلة (6+ أرقام)
    
    // استخراج الرقم الفعلي فقط (مع الفاصلة العشرية)
    preg_match('/[\d]+\.?[\d]*/', $priceStr, $matches);
    if (!empty($matches)) {
        $priceStr = $matches[0];
    }
    
    // التأكد من أن السعر رقم
    $price = floatval($priceStr);
    
    // تنظيف السعر من أي قيم غير صحيحة
    if (!is_numeric($price) || $price < 0 || $price > 999999) {
        $price = 0;
    }
    
    // تنسيق السعر (استخدام الفاصلة العشرية والنقطة للآلاف)
    $formatted = number_format($price, 2, '.', '');
    
    // رمز العملة - استخدام قيمة ثابتة مباشرة
    $currency = 'DH';
    
    // إرجاع السعر مع رمز العملة فقط
    return $formatted . ' ' . $currency;
}

// دالة للحصول على المنتجات الأكثر مبيعاً
function getBestSellers($limit = 6) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM products WHERE best_seller = 1 ORDER BY rating DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    closeDBConnection($conn);
    return $products;
}

// دالة للحصول على العروض الخاصة
function getPromoProducts($limit = 4) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM products WHERE promo = 1 ORDER BY rating DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    closeDBConnection($conn);
    return $products;
}

require_once 'database.php';
?>

