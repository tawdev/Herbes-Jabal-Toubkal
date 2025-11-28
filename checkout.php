<?php
$pageTitle = "إتمام الطلب";
require_once 'config/config.php';
require_once 'includes/header.php';

// قراءة السلة من SESSION
$cartItems = $_SESSION['cart'] ?? [];

if (empty($cartItems)) {
    redirect('cart.php');
}

// التأكد من أن جميع الكميات صحيحة (أكبر من 0)
foreach ($cartItems as $productId => $item) {
    if (!isset($item['quantity']) || $item['quantity'] < 1) {
        unset($cartItems[$productId]);
        unset($_SESSION['cart'][$productId]);
    }
}

// تحديث SESSION بعد التنظيف
$_SESSION['cart'] = $cartItems;

// معالجة إرسال الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $conn = getDBConnection();
    
    $userName = $_POST['user_name'];
    $userEmail = $_POST['user_email'];
    $userPhone = $_POST['user_phone'];
    $userAddress = $_POST['user_address'];
    
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // إدراج الطلب
    $sql = "INSERT INTO orders (user_name, user_email, user_phone, user_address, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssd", $userName, $userEmail, $userPhone, $userAddress, $total);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();
    
    // إدراج عناصر الطلب
    foreach ($cartItems as $productId => $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $orderId, $productId, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }
    
    closeDBConnection($conn);
    
    // تفريغ السلة
    $_SESSION['cart'] = [];
    
    echo '<div class="container"><section class="section"><div style="text-align: center; padding: 4rem 2rem; background-color: var(--white); border-radius: 10px; box-shadow: var(--shadow);">';
    echo '<h2 style="color: var(--primary-color); margin-bottom: 1rem;">تم إرسال طلبك بنجاح!</h2>';
    echo '<p style="font-size: 1.125rem; color: var(--text-light); margin-bottom: 2rem;">رقم الطلب: #' . $orderId . '</p>';
    echo '<a href="index.php" class="btn">العودة إلى الرئيسية</a>';
    echo '</div></section></div>';
    require_once 'includes/footer.php';
    exit;
}

// حساب الإجمالي
$total = 0;
foreach ($cartItems as $productId => $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="container">
    <section class="section">
        <h2 class="section-title">إتمام الطلب</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- نموذج الطلب -->
            <div style="background-color: var(--white); border-radius: 10px; box-shadow: var(--shadow); padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--dark-color);">معلومات التوصيل</h3>
                <form method="POST" action="checkout.php">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الاسم الكامل *</label>
                        <input type="text" name="user_name" required 
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">البريد الإلكتروني *</label>
                        <input type="email" name="user_email" required 
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">رقم الهاتف *</label>
                        <input type="tel" name="user_phone" required 
                               style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <label style="font-weight: 600;">عنوان التوصيل *</label>
                            <button type="button" id="getLocationBtn" 
                                    style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-size: 0.9rem;">
                                <span style="font-size: 1.1rem;">📍</span>
                                <span>تحديد الموقع تلقائياً</span>
                            </button>
                        </div>
                        <div style="position: relative;">
                            <textarea name="user_address" id="user_address" required rows="4" 
                                      style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem; resize: vertical; transition: border-color 0.3s ease;"
                                      placeholder="أدخل عنوان التوصيل الكامل أو استخدم زر تحديد الموقع"></textarea>
                        </div>
                        <div id="locationStatus" style="margin-top: 0.5rem; font-size: 0.875rem; min-height: 20px; display: flex; align-items: center; gap: 0.5rem;">
                            <span id="locationIcon"></span>
                            <span id="locationText"></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn" style="width: 100%;">
                        تأكيد الطلب
                    </button>
                </form>
            </div>
            
            <!-- ملخص الطلب -->
            <div style="background-color: var(--white); border-radius: 10px; box-shadow: var(--shadow); padding: 2rem; height: fit-content; position: sticky; top: 100px;">
                <h3 style="margin-bottom: 1.5rem; color: var(--dark-color); border-bottom: 2px solid var(--light-color); padding-bottom: 0.75rem;">ملخص الطلب</h3>
                <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1rem;">
                    <?php 
                    $summaryTotal = 0;
                    foreach ($cartItems as $productId => $item): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $summaryTotal += $itemTotal;
                    ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-color); margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">
                                    <?php echo formatPrice($item['price']); ?> × <?php echo $item['quantity']; ?>
                                </div>
                            </div>
                            <div style="font-weight: 600; color: var(--primary-color); margin-right: 1rem; min-width: 100px; text-align: left;">
                                <?php echo formatPrice($itemTotal); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.5rem; font-weight: 700; color: var(--primary-color); padding-top: 1rem; border-top: 2px solid var(--light-color);">
                    <span>الإجمالي:</span>
                    <span id="checkout-total"><?php echo formatPrice($summaryTotal); ?></span>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
$additionalScripts = ['assets/js/checkout.js'];
require_once 'includes/footer.php'; 
?>

