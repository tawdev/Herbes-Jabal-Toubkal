<?php
$pageTitle = "تفاصيل الوصفة";
require_once 'config/config.php';
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    redirect('recipes.php');
}

$recipeId = intval($_GET['id']);
$conn = getDBConnection();

$sql = "SELECT * FROM recipes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if (!$recipe) {
    redirect('recipes.php');
}
?>

<div class="container">
    <section class="section">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background-color: var(--white); border-radius: 10px; overflow: hidden; box-shadow: var(--shadow);">
                <img src="<?php echo BASE_URL . $recipe['image']; ?>" 
                     alt="<?php echo htmlspecialchars($recipe['title_ar']); ?>" 
                     style="width: 100%; height: 400px; object-fit: cover;"
                     onerror="this.src='images/placeholder.jpg'">
                
                <div style="padding: 3rem;">
                    <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--dark-color);">
                        <?php echo htmlspecialchars($recipe['title_ar']); ?>
                    </h1>
                    
                    <?php if ($recipe['description_ar'] || $recipe['description']): ?>
                        <p style="font-size: 1.125rem; color: var(--text-light); margin-bottom: 2rem; line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($recipe['description_ar'] ?: $recipe['description'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div style="display: flex; gap: 2rem; margin-bottom: 3rem; flex-wrap: wrap;">
                        <?php if ($recipe['cooking_time']): ?>
                            <div>
                                <strong>⏱️ وقت الطبخ:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($recipe['difficulty']): ?>
                            <div>
                                <strong>📊 الصعوبة:</strong> <?php echo htmlspecialchars($recipe['difficulty']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin-bottom: 3rem;">
                        <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--primary-color);">المكونات</h2>
                        <div style="background-color: var(--light-color); padding: 1.5rem; border-radius: 5px;">
                            <ul style="list-style: none; padding: 0;">
                                <?php
                                $ingredients = explode("\n", $recipe['ingredients_ar'] ?: $recipe['ingredients']);
                                foreach ($ingredients as $ingredient) {
                                    if (trim($ingredient)) {
                                        echo '<li style="padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.1);">✓ ' . htmlspecialchars(trim($ingredient)) . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 3rem;">
                        <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--primary-color);">طريقة التحضير</h2>
                        <div style="background-color: var(--white); padding: 1.5rem; border-radius: 5px; border: 2px solid var(--light-color);">
                            <ol style="padding-right: 1.5rem; line-height: 2;">
                                <?php
                                $steps = explode("\n", $recipe['steps_ar'] ?: $recipe['steps']);
                                foreach ($steps as $index => $step) {
                                    if (trim($step)) {
                                        echo '<li style="margin-bottom: 1rem; color: var(--text-color);">' . htmlspecialchars(trim($step)) . '</li>';
                                    }
                                }
                                ?>
                            </ol>
                        </div>
                    </div>
                    
                    <?php if ($recipe['spices_used']): ?>
                    <div style="margin-bottom: 2rem;">
                        <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--primary-color);">التوابل المستخدمة</h2>
                        <p style="color: var(--text-light); line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($recipe['spices_used'])); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 3rem; text-align: center;">
                        <a href="recipes.php" class="btn">العودة إلى الوصفات</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

