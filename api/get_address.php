<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// معالجة OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['lat']) || !isset($data['lon'])) {
    echo json_encode(['success' => false, 'message' => 'Missing latitude or longitude']);
    exit;
}

$lat = floatval($data['lat']);
$lon = floatval($data['lon']);

// بناء URL لـ Nominatim API
$nominatimUrl = sprintf(
    'https://nominatim.openstreetmap.org/reverse?format=json&lat=%.6f&lon=%.6f&zoom=18&addressdetails=1&accept-language=ar',
    $lat,
    $lon
);

// إعداد cURL للاتصال بـ Nominatim
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $nominatimUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Tawabil-Spices-Store/1.0 (Contact: info@tawabil.com)');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Accept-Language: ar,en'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => 'Connection error: ' . $error,
        'fallback' => sprintf('خط العرض: %.6f, خط الطول: %.6f', $lat, $lon)
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'success' => false,
        'message' => 'HTTP error: ' . $httpCode,
        'fallback' => sprintf('خط العرض: %.6f, خط الطول: %.6f', $lat, $lon)
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid response from server',
        'fallback' => sprintf('خط العرض: %.6f, خط الطول: %.6f', $lat, $lon)
    ]);
    exit;
}

// بناء العنوان
$fullAddress = '';
if (isset($data['address']) && is_array($data['address'])) {
    $address = $data['address'];
    
    if (isset($address['road'])) $fullAddress .= $address['road'] . '، ';
    if (isset($address['house_number'])) $fullAddress .= $address['house_number'] . '، ';
    if (isset($address['neighbourhood']) || isset($address['suburb'])) {
        $fullAddress .= (isset($address['neighbourhood']) ? $address['neighbourhood'] : $address['suburb']) . '، ';
    }
    if (isset($address['city']) || isset($address['town']) || isset($address['village'])) {
        $city = isset($address['city']) ? $address['city'] : (isset($address['town']) ? $address['town'] : $address['village']);
        $fullAddress .= $city . '، ';
    }
    if (isset($address['state'])) $fullAddress .= $address['state'] . '، ';
    if (isset($address['country'])) $fullAddress .= $address['country'];
    
    // تنظيف الفواصل الزائدة
    $fullAddress = preg_replace('/،\s*$/', '', $fullAddress);
    $fullAddress = trim($fullAddress);
}

// إذا لم يكن هناك عنوان، استخدم display_name
if (empty($fullAddress) && isset($data['display_name'])) {
    $fullAddress = $data['display_name'];
}

// إذا لم يكن هناك شيء، استخدم الإحداثيات
if (empty($fullAddress)) {
    $fullAddress = sprintf('خط العرض: %.6f, خط الطول: %.6f', $lat, $lon);
}

echo json_encode([
    'success' => true,
    'address' => $fullAddress,
    'raw_data' => $data
]);
?>

