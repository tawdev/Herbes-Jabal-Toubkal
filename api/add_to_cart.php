<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$productId = intval($data['product_id']);
$quantity = intval($data['quantity']);

// التحقق من وجود المنتج
$conn = getDBConnection();
$sql = "SELECT id, name_ar, price, promo_price FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// إضافة المنتج للسلة
$price = $product['promo_price'] ?: $product['price'];

if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$productId] = [
        'product_id' => $productId,
        'name' => $product['name_ar'],
        'price' => $price,
        'quantity' => $quantity
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'تم إضافة المنتج إلى السلة',
    'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
]);
?>

