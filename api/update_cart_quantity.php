<?php
session_start();
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

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

// تحديث الكمية في SESSION
if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] = $quantity;
    
    // حساب المجموع
    $itemTotal = $_SESSION['cart'][$productId]['price'] * $quantity;
    $cartTotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تحديث الكمية بنجاح',
        'item_total' => $itemTotal,
        'cart_total' => $cartTotal
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found in cart']);
}
?>

