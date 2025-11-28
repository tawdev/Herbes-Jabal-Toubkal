<?php
// إعدادات عامة للموقع
session_start();

// إعدادات اللغة
define('SITE_NAME', 'Tawabil - التوابل والبهارات');
define('CURRENCY', 'درهم');
define('CURRENCY_SYMBOL', 'د.م.');

// مسارات الملفات
define('BASE_URL', 'http://localhost/tawabil/');
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
    return number_format($price, 2) . ' ' . CURRENCY_SYMBOL;
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

