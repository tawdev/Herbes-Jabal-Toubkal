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
        <a href="shop.php" class="btn" style="background-color: #6b7280; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            العودة إلى المتجر
                        </a>
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
                            <tr style="border-bottom: 3px solid var(--primary-color);">
                                <th style="padding: 1.5rem; text-align: right; font-size: 1.25rem; font-weight: 700; color: var(--dark-color);">المنتج</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 1.25rem; font-weight: 700; color: var(--dark-color);">السعر</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 1.25rem; font-weight: 700; color: var(--dark-color);">الكمية</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 1.25rem; font-weight: 700; color: var(--dark-color);">المجموع</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 1.25rem; font-weight: 700; color: var(--dark-color);">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $productId => $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $total += $itemTotal;
                            ?>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 1.5rem;">
                                        <strong style="font-size: 1.15rem; font-weight: 700; color: var(--dark-color);"><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center;">
                                        <span style="font-size: 1.15rem; font-weight: 700; color: var(--text-color);"><?php echo formatPrice($item['price']); ?></span>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center;">
                                        <input type="number" 
                                               name="quantity[<?php echo $productId; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               class="quantity-input"
                                               data-product-id="<?php echo $productId; ?>"
                                               data-price="<?php echo $item['price']; ?>"
                                               style="width: 90px; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; text-align: center; font-size: 1.1rem; font-weight: 700;"
                                               onchange="updateItemTotal(<?php echo $productId; ?>, <?php echo $item['price']; ?>)">
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center; font-weight: 700; color: var(--primary-color);">
                                        <span class="item-total" data-product-id="<?php echo $productId; ?>" style="font-size: 1.2rem; font-weight: 700;">
                                            <?php echo formatPrice($itemTotal); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="cart.php?remove=<?php echo $productId; ?>" 
                                           style="color: #ef4444; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 5px; transition: all 0.3s ease;"
                                           title="حذف المنتج"
                                           onmouseover="this.style.backgroundColor='#fee2e2'; this.style.transform='scale(1.1)'"
                                           onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)'">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: var(--light-color);">
                                <td colspan="3" style="padding: 1.5rem; text-align: left; font-weight: 700; font-size: 1.5rem; color: var(--dark-color);">
                                    الإجمالي:
                                </td>
                                <td colspan="2" style="padding: 1.5rem; text-align: center; font-weight: 700; font-size: 1.75rem; color: var(--primary-color);">
                                    <span id="cart-total" style="font-weight: 700;"><?php echo formatPrice($total); ?></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: space-between; flex-wrap: wrap;">
                        
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <button type="submit" name="update_cart" class="btn" style="background-color: var(--text-light);">
                                تحديث السلة
                            </button>
                            <a href="checkout.php" class="btn">
                                إتمام الطلب
                            </a>
                        </div>
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

