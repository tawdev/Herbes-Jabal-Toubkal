<?php
$pageTitle = "الرئيسية";
require_once 'config/config.php';
require_once 'includes/header.php';

$bestSellers = getBestSellers(6);
$promoProducts = getPromoProducts(4);
?>

<!-- قسم البطل (Hero Section) -->
<section class="hero-section">
    <?php
    // صورة واحدة للقسم الرئيسي
    $heroImage = BASE_URL . 'images/slider/1764327685_Health Benefits of Chili, Chili Peppers, and Chili Powder.jpeg';
    $heroImage = str_replace([' ', '(', ')'], ['%20', '%28', '%29'], $heroImage);
    ?>
    <div class="hero-image" style="background-image: url('<?php echo htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8'); ?>');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2>اكتشف عالم التوابل</h2>
            <p>أجود أنواع التوابل والبهارات المغربية والعالمية</p>
            <a href="shop.php" class="btn">تسوق الآن</a>
        </div>
    </div>
</section>

<div class="container">
    <!-- قسم أفضل المنتجات مبيعاً -->
    <section class="section">
        <h2 class="section-title">أفضل التوابل مبيعاً</h2>
        <div class="products-grid">
            <?php foreach ($bestSellers as $product): ?>
                <div class="product-card">
                    <?php if ($product['promo']): ?>
                        <span class="product-badge">عرض خاص</span>
                    <?php endif; ?>
                    <img src="<?php echo BASE_URL . $product['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['name_ar']); ?>" 
                         class="product-image"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name_ar']); ?></h3>
                        <p class="product-weight"><?php echo htmlspecialchars($product['weight']); ?></p>
                        <p class="product-description">
                            <?php echo htmlspecialchars(mb_substr($product['description_ar'] ?: $product['description'], 0, 100)); ?>...
                        </p>
                        <div class="product-footer">
                            <div>
                                <?php if ($product['promo'] && $product['promo_price']): ?>
                                    <span class="product-price"><?php echo formatPrice($product['promo_price']); ?></span>
                                    <span class="product-price-old"><?php echo formatPrice($product['price']); ?></span>
                                <?php else: ?>
                                    <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="add-to-cart" 
                                    onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_ar'], ENT_QUOTES); ?>', <?php echo $product['promo'] && $product['promo_price'] ? $product['promo_price'] : $product['price']; ?>)">
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- قسم العروض الخاصة -->
    <?php if (!empty($promoProducts)): ?>
    <section class="section promo-section">
        <h2 class="section-title">عروض خاصة</h2>
        <div class="products-grid">
            <?php foreach ($promoProducts as $product): ?>
                <div class="product-card">
                    <span class="product-badge">عرض خاص</span>
                    <img src="<?php echo BASE_URL . $product['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['name_ar']); ?>" 
                         class="product-image"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name_ar']); ?></h3>
                        <p class="product-weight"><?php echo htmlspecialchars($product['weight']); ?></p>
                        <p class="product-description">
                            <?php echo htmlspecialchars(mb_substr($product['description_ar'] ?: $product['description'], 0, 100)); ?>...
                        </p>
                        <div class="product-footer">
                            <div>
                                <span class="product-price"><?php echo formatPrice($product['promo_price'] ?: $product['price']); ?></span>
                                <?php if ($product['promo_price']): ?>
                                    <span class="product-price-old"><?php echo formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="add-to-cart" 
                                    onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_ar'], ENT_QUOTES); ?>', <?php echo $product['promo_price'] ?: $product['price']; ?>)">
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- قسم لماذا تختارنا -->
    <section class="section" id="about">
        <h2 class="section-title">لماذا تختارنا؟</h2>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">جودة عالية</h3>
                <p class="feature-description">نختار أجود أنواع التوابل من مصادر موثوقة لضمان الجودة والنكهة الأصيلة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">توصيل سريع</h3>
                <p class="feature-description">خدمة توصيل سريعة وآمنة لجميع أنحاء المغرب خلال 24-48 ساعة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">أسعار مناسبة</h3>
                <p class="feature-description">أفضل الأسعار في السوق مع عروض وخصومات حصرية على منتجاتنا</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3 class="feature-title">طبيعي 100%</h3>
                <p class="feature-description">جميع منتجاتنا طبيعية بدون إضافات أو مواد حافظة</p>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

