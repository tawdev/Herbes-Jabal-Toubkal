<?php
/**
 * سكريبت لتشخيص مشكلة 262145 في الأسعار
 */

require_once 'config/config.php';

$conn = getDBConnection();

echo "<!DOCTYPE html><html lang='ar' dir='rtl'><head><meta charset='UTF-8'><title>تشخيص الأسعار</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .box { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: right; }
    th { background: #d97706; color: white; }
    .error { color: red; font-weight: bold; }
    .ok { color: green; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style></head><body>";
echo "<h1>🔍 تشخيص مشكلة الأسعار</h1>";

// فحص منتج واحد بالتفصيل
$sql = "SELECT * FROM products WHERE id = 1 OR promo = 1 LIMIT 3";
$result = $conn->query($sql);

echo "<div class='box'>";
echo "<h2>فحص المنتجات بالتفصيل:</h2>";

while ($row = $result->fetch_assoc()) {
    echo "<h3>المنتج ID: {$row['id']} - {$row['name_ar']}</h3>";
    
    echo "<table>";
    echo "<tr><th>الحقل</th><th>القيمة الخام</th><th>الطول</th><th>Bytes</th><th>formatPrice()</th></tr>";
    
    // فحص price
    $price = $row['price'];
    $priceBytes = [];
    foreach (str_split($price) as $char) {
        $priceBytes[] = ord($char);
    }
    $formattedPrice = formatPrice($price);
    $hasError = strpos($formattedPrice, '262145') !== false || strpos($price, '262145') !== false;
    
    echo "<tr>";
    echo "<td><strong>price</strong></td>";
    echo "<td>" . htmlspecialchars($price) . "</td>";
    echo "<td>" . strlen($price) . "</td>";
    echo "<td><pre>" . implode(' ', $priceBytes) . "</pre></td>";
    echo "<td class='" . ($hasError ? 'error' : 'ok') . "'>" . htmlspecialchars($formattedPrice) . "</td>";
    echo "</tr>";
    
    // فحص promo_price
    if ($row['promo_price']) {
        $promoPrice = $row['promo_price'];
        $promoBytes = [];
        foreach (str_split($promoPrice) as $char) {
            $promoBytes[] = ord($char);
        }
        $formattedPromo = formatPrice($promoPrice);
        $hasErrorPromo = strpos($formattedPromo, '262145') !== false || strpos($promoPrice, '262145') !== false;
        
        echo "<tr>";
        echo "<td><strong>promo_price</strong></td>";
        echo "<td>" . htmlspecialchars($promoPrice) . "</td>";
        echo "<td>" . strlen($promoPrice) . "</td>";
        echo "<td><pre>" . implode(' ', $promoBytes) . "</pre></td>";
        echo "<td class='" . ($hasErrorPromo ? 'error' : 'ok') . "'>" . htmlspecialchars($formattedPromo) . "</td>";
        echo "</tr>";
    }
    
    echo "</table><br>";
}
echo "</div>";

// فحص CURRENCY_SYMBOL
echo "<div class='box'>";
echo "<h2>فحص CURRENCY_SYMBOL:</h2>";
$currency = CURRENCY_SYMBOL;
$currencyBytes = [];
foreach (str_split($currency) as $char) {
    $currencyBytes[] = ord($char);
}
echo "<p><strong>القيمة:</strong> " . htmlspecialchars($currency) . "</p>";
echo "<p><strong>الطول:</strong> " . strlen($currency) . "</p>";
echo "<p><strong>Bytes:</strong> <pre>" . implode(' ', $currencyBytes) . "</pre></p>";
echo "</div>";

// اختبار formatPrice مباشرة
echo "<div class='box'>";
echo "<h2>اختبار formatPrice() بقيم مختلفة:</h2>";
echo "<table>";
echo "<tr><th>القيمة المدخلة</th><th>formatPrice()</th><th>النتيجة</th></tr>";

$testValues = [
    '85.00',
    '45.00',
    '120.00',
    '85',
    '262145',
    '262145 85.00'
];

foreach ($testValues as $testVal) {
    $result = formatPrice($testVal);
    $hasError = strpos($result, '262145') !== false;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($testVal) . "</td>";
    echo "<td>" . htmlspecialchars($result) . "</td>";
    echo "<td class='" . ($hasError ? 'error' : 'ok') . "'>" . ($hasError ? '❌' : '✓') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

$conn->close();
echo "</body></html>";
?>

