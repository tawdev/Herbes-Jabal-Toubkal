<?php
$pageTitle = "تفاصيل المنتج";
require_once 'config/config.php';
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    redirect('shop.php');
}

$productId = intval($_GET['id']);
$conn = getDBConnection();

// جلب بيانات المنتج
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    redirect('shop.php');
}

// جلب التقييمات
$sql = "SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// جلب منتجات مشابهة
$sql = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $product['category'], $productId);
$stmt->execute();
$result = $stmt->get_result();
$relatedProducts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeDBConnection($conn);
?>

<div class="container">
    <section class="section">
        <div class="product-details">
            <!-- صورة المنتج -->
            <div class="product-image-large">
                <img src="<?php echo BASE_URL . $product['image']; ?>" 
                     alt="<?php echo htmlspecialchars($product['name_ar']); ?>"
                     onclick="zoomImage(this)"
                     onerror="this.src='images/placeholder.jpg'"
                     style="cursor: zoom-in; transition: transform 0.3s;">
            </div>
            
            <!-- معلومات المنتج -->
            <div class="product-info-details">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name_ar']); ?></h1>
                
                <div class="product-rating">
                    <div class="stars">
                        <?php
                        $rating = floatval($product['rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '★';
                            } else if ($i - 0.5 <= $rating) {
                                echo '☆';
                            } else {
                                echo '☆';
                            }
                        }
                        ?>
                    </div>
                    <span>(<?php echo $rating; ?>)</span>
                </div>
                
                <div class="product-price-large">
                    <?php if ($product['promo'] && $product['promo_price']): ?>
                        <span><?php echo formatPrice($product['promo_price']); ?></span>
                        <span class="product-price-old" style="font-size: 1.5rem; margin-right: 1rem;">
                            <?php echo formatPrice($product['price']); ?>
                        </span>
                    <?php else: ?>
                        <span><?php echo formatPrice($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <p style="margin: 1.5rem 0; color: var(--text-light); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($product['description_ar'] ?: $product['description'])); ?>
                </p>
                
                <button class="btn" style="width: 100%; margin-top: 2rem;"
                        onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_ar'], ENT_QUOTES); ?>', <?php echo $product['promo'] && $product['promo_price'] ? $product['promo_price'] : $product['price']; ?>)">
                    أضف إلى السلة
                </button>
                
                <!-- مواصفات المنتج -->
                <div class="product-specs">
                    <div class="spec-item">
                        <span class="spec-label">الوزن:</span>
                        <span><?php echo htmlspecialchars($product['weight']); ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">التصنيف:</span>
                        <span>
                            <?php
                            $categories = [
                                'spices' => 'توابل',
                                'herbs' => 'أعشاب',
                                'mixes' => 'خلطات',
                                'ground' => 'مطحونة',
                                'whole' => 'كاملة'
                            ];
                            echo $categories[$product['category']] ?? $product['category'];
                            ?>
                        </span>
                    </div>
                    <?php if ($product['origin_country']): ?>
                    <div class="spec-item">
                        <span class="spec-label">بلد المنشأ:</span>
                        <span><?php echo htmlspecialchars($product['origin_country']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="spec-item">
                        <span class="spec-label">المخزون:</span>
                        <span><?php echo $product['stock'] > 0 ? 'متوفر (' . $product['stock'] . ')' : 'غير متوفر'; ?></span>
                    </div>
                </div>
                
                <?php if ($product['ingredients']): ?>
                <div style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">المكونات:</h3>
                    <p style="color: var(--text-light);"><?php echo nl2br(htmlspecialchars($product['ingredients'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($product['benefits']): ?>
                <div style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">الفوائد:</h3>
                    <p style="color: var(--text-light);"><?php echo nl2br(htmlspecialchars($product['benefits'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($product['usage_method']): ?>
                <div style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">طريقة الاستخدام:</h3>
                    <p style="color: var(--text-light);"><?php echo nl2br(htmlspecialchars($product['usage_method'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- التقييمات -->
        <?php if (!empty($reviews)): ?>
        <div class="reviews-section">
            <h2 style="margin-bottom: 2rem;">تقييمات العملاء</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-author"><?php echo htmlspecialchars($review['user_name']); ?></span>
                        <div class="stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $review['rating'] ? '★' : '☆';
                            }
                            ?>
                        </div>
                    </div>
                    <?php if ($review['comment']): ?>
                        <p style="margin-top: 0.5rem; color: var(--text-light);">
                            <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                        </p>
                    <?php endif; ?>
                    <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-light);">
                        <?php echo date('Y-m-d', strtotime($review['created_at'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- منتجات مشابهة -->
        <?php if (!empty($relatedProducts)): ?>
        <div style="margin-top: 4rem;">
            <h2 class="section-title">منتجات مشابهة</h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $related['id']; ?>">
                            <img src="<?php echo BASE_URL . $related['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($related['name_ar']); ?>" 
                                 class="product-image"
                                 onerror="this.src='images/placeholder.jpg'">
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="product.php?id=<?php echo $related['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($related['name_ar']); ?>
                                </a>
                            </h3>
                            <p class="product-weight"><?php echo htmlspecialchars($related['weight']); ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($related['price']); ?></span>
                                <button class="add-to-cart" 
                                        onclick="addToCart(<?php echo $related['id']; ?>, '<?php echo htmlspecialchars($related['name_ar'], ENT_QUOTES); ?>', <?php echo $related['price']; ?>)">
                                    أضف للسلة
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
</div>

<script>
function zoomImage(img) {
    if (img.style.transform === 'scale(2)') {
        img.style.transform = 'scale(1)';
        img.style.cursor = 'zoom-in';
    } else {
        img.style.transform = 'scale(2)';
        img.style.cursor = 'zoom-out';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

