<?php
$pageTitle = "المتجر";
require_once 'config/config.php';
require_once 'includes/header.php';

$conn = getDBConnection();

// معالجة الفلترة والترتيب
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// بناء الاستعلام
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (name_ar LIKE ? OR description_ar LIKE ? OR name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

// الترتيب
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    case 'bestseller':
        $sql .= " ORDER BY best_seller DESC, rating DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);
?>

<div class="container">
    <section class="section">
        <h2 class="section-title">المتجر</h2>
        
        <!-- الفلاتر -->
        <div class="filters-section">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="categoryFilter">التصنيف:</label>
                    <select id="categoryFilter" onchange="filterProducts()">
                        <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>الكل</option>
                        <option value="spices" <?php echo $category === 'spices' ? 'selected' : ''; ?>>توابل</option>
                        <option value="herbs" <?php echo $category === 'herbs' ? 'selected' : ''; ?>>أعشاب</option>
                        <option value="mixes" <?php echo $category === 'mixes' ? 'selected' : ''; ?>>خلطات</option>
                        <option value="ground" <?php echo $category === 'ground' ? 'selected' : ''; ?>>مطحونة</option>
                        <option value="whole" <?php echo $category === 'whole' ? 'selected' : ''; ?>>كاملة</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sortFilter">الترتيب حسب:</label>
                    <select id="sortFilter" onchange="filterProducts()">
                        <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>الافتراضي</option>
                        <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>السعر: من الأقل للأعلى</option>
                        <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>السعر: من الأعلى للأقل</option>
                        <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>التقييم</option>
                        <option value="bestseller" <?php echo $sort === 'bestseller' ? 'selected' : ''; ?>>الأكثر مبيعاً</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- نتائج البحث -->
        <?php if (!empty($search)): ?>
            <p style="margin-bottom: 2rem; color: var(--text-light);">
                نتائج البحث عن: "<strong><?php echo htmlspecialchars($search); ?></strong>" 
                (<?php echo count($products); ?> منتج)
            </p>
        <?php endif; ?>
        
        <!-- قائمة المنتجات -->
        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <p style="font-size: 1.25rem; color: var(--text-light);">لم يتم العثور على منتجات</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['promo']): ?>
                            <span class="product-badge">عرض خاص</span>
                        <?php endif; ?>
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo BASE_URL . $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name_ar']); ?>" 
                                 class="product-image"
                                 onerror="this.src='images/placeholder.jpg'">
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($product['name_ar']); ?>
                                </a>
                            </h3>
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
        <?php endif; ?>
    </section>
</div>

<?php 
$additionalScripts = ['assets/js/shop.js'];
require_once 'includes/footer.php'; 
?>

