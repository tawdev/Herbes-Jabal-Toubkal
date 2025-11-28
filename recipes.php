<?php
$pageTitle = "الوصفات";
require_once 'config/config.php';
require_once 'includes/header.php';

$conn = getDBConnection();
$sql = "SELECT * FROM recipes ORDER BY created_at DESC";
$result = $conn->query($sql);
$recipes = $result->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);
?>

<div class="container">
    <section class="section">
        <h2 class="section-title">وصفات الطبخ</h2>
        
        <?php if (empty($recipes)): ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <p style="font-size: 1.25rem; color: var(--text-light);">لا توجد وصفات متاحة حالياً</p>
            </div>
        <?php else: ?>
            <div class="recipes-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
                        <img src="<?php echo BASE_URL . $recipe['image']; ?>" 
                             alt="<?php echo htmlspecialchars($recipe['title_ar']); ?>" 
                             class="recipe-image"
                             onerror="this.src='images/placeholder.jpg'">
                        <div class="recipe-info">
                            <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title_ar']); ?></h3>
                            <p class="recipe-description">
                                <?php echo htmlspecialchars(mb_substr($recipe['description_ar'] ?: $recipe['description'], 0, 150)); ?>...
                            </p>
                            <?php if ($recipe['cooking_time']): ?>
                                <p style="color: var(--text-light); margin-bottom: 1rem;">
                                    ⏱️ وقت الطبخ: <?php echo htmlspecialchars($recipe['cooking_time']); ?>
                                </p>
                            <?php endif; ?>
                            <a href="recipe-details.php?id=<?php echo $recipe['id']; ?>" class="btn" style="display: inline-block; text-decoration: none;">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

