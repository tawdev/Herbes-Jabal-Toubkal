<?php
$pageTitle = "السلة";
require_once 'config/config.php';
require_once 'includes/header.php';

// معالجة حذف منتج من السلة
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    redirect('cart.php');
}

// معالجة تحديث الكمية
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $productId => $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = intval($quantity);
            } else {
                unset($_SESSION['cart'][$productId]);
            }
        }
    }
    redirect('cart.php');
}

$cartItems = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="container">
    <section class="section">
        <h2 class="section-title">سلة التسوق</h2>
        
        <?php if (empty($cartItems)): ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <p style="font-size: 1.25rem; color: var(--text-light); margin-bottom: 2rem;">سلة التسوق فارغة</p>
                <a href="shop.php" class="btn">تسوق الآن</a>
            </div>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <div style="background-color: var(--white); border-radius: 10px; box-shadow: var(--shadow); padding: 2rem; margin-bottom: 2rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb;">
                                <th style="padding: 1rem; text-align: right;">المنتج</th>
                                <th style="padding: 1rem; text-align: center;">السعر</th>
                                <th style="padding: 1rem; text-align: center;">الكمية</th>
                                <th style="padding: 1rem; text-align: center;">المجموع</th>
                                <th style="padding: 1rem; text-align: center;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $productId => $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $total += $itemTotal;
                            ?>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 1rem;">
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <?php echo formatPrice($item['price']); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <input type="number" 
                                               name="quantity[<?php echo $productId; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               class="quantity-input"
                                               data-product-id="<?php echo $productId; ?>"
                                               data-price="<?php echo $item['price']; ?>"
                                               style="width: 80px; padding: 0.5rem; border: 2px solid #e5e7eb; border-radius: 5px; text-align: center;"
                                               onchange="updateItemTotal(<?php echo $productId; ?>, <?php echo $item['price']; ?>)">
                                    </td>
                                    <td style="padding: 1rem; text-align: center; font-weight: 600; color: var(--primary-color);">
                                        <span class="item-total" data-product-id="<?php echo $productId; ?>">
                                            <?php echo formatPrice($itemTotal); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="cart.php?remove=<?php echo $productId; ?>" 
                                           style="color: #ef4444; text-decoration: none; font-weight: 600;">
                                            حذف
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding: 1rem; text-align: left; font-weight: 700; font-size: 1.25rem;">
                                    الإجمالي:
                                </td>
                                <td colspan="2" style="padding: 1rem; text-align: center; font-weight: 700; font-size: 1.5rem; color: var(--primary-color);">
                                    <span id="cart-total"><?php echo formatPrice($total); ?></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: space-between; flex-wrap: wrap;">
                        <button type="submit" name="update_cart" class="btn" style="background-color: var(--text-light);">
                            تحديث السلة
                        </button>
                        <a href="checkout.php" class="btn">
                            إتمام الطلب
                        </a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </section>
</div>

<?php 
$additionalScripts = ['assets/js/cart.js'];
require_once 'includes/footer.php'; 
?>

