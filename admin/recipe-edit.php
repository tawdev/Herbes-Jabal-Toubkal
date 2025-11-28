<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

if (!isset($_GET['id'])) {
    redirect('admin/recipes.php');
}

$conn = getDBConnection();
$recipeId = intval($_GET['id']);

// جلب بيانات الوصفة
$sql = "SELECT * FROM recipes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

if (!$recipe) {
    redirect('admin/recipes.php');
}

// معالجة التعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $title_ar = $_POST['title_ar'];
    $description = $_POST['description'] ?? '';
    $description_ar = $_POST['description_ar'] ?? '';
    $ingredients = $_POST['ingredients'];
    $ingredients_ar = $_POST['ingredients_ar'];
    $steps = $_POST['steps'];
    $steps_ar = $_POST['steps_ar'];
    $spices_used = $_POST['spices_used'] ?? '';
    $cooking_time = $_POST['cooking_time'] ?? '';
    $difficulty = $_POST['difficulty'] ?? '';
    
    $image = $recipe['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/recipes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'images/recipes/' . $fileName;
        }
    }
    
    $sql = "UPDATE recipes SET title=?, title_ar=?, description=?, description_ar=?, image=?, ingredients=?, ingredients_ar=?, steps=?, steps_ar=?, spices_used=?, cooking_time=?, difficulty=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", $title, $title_ar, $description, $description_ar, $image, $ingredients, $ingredients_ar, $steps, $steps_ar, $spices_used, $cooking_time, $difficulty, $recipeId);
    $stmt->execute();
    $stmt->close();
    closeDBConnection($conn);
    
    redirect('admin/recipes.php');
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل وصفة</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }
        .form-container {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>🌶️ تعديل وصفة</h2>
                <div>
                    <a href="recipes.php">العودة</a>
                    <a href="../index.php">الموقع</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>العنوان (إنجليزي) *</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>العنوان (عربي) *</label>
                        <input type="text" name="title_ar" value="<?php echo htmlspecialchars($recipe['title_ar']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>الوصف (إنجليزي)</label>
                    <textarea name="description"><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>الوصف (عربي)</label>
                    <textarea name="description_ar"><?php echo htmlspecialchars($recipe['description_ar']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>المكونات (إنجليزي) * (سطر واحد لكل مكون)</label>
                    <textarea name="ingredients" required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>المكونات (عربي) * (سطر واحد لكل مكون)</label>
                    <textarea name="ingredients_ar" required><?php echo htmlspecialchars($recipe['ingredients_ar']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>خطوات التحضير (إنجليزي) * (سطر واحد لكل خطوة)</label>
                    <textarea name="steps" required><?php echo htmlspecialchars($recipe['steps']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>خطوات التحضير (عربي) * (سطر واحد لكل خطوة)</label>
                    <textarea name="steps_ar" required><?php echo htmlspecialchars($recipe['steps_ar']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>التوابل المستخدمة</label>
                        <input type="text" name="spices_used" value="<?php echo htmlspecialchars($recipe['spices_used']); ?>">
                    </div>
                    <div class="form-group">
                        <label>الصورة (اتركه فارغاً للاحتفاظ بالصورة الحالية)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if ($recipe['image']): ?>
                            <img src="../<?php echo $recipe['image']; ?>" style="width: 150px; height: 150px; object-fit: cover; margin-top: 0.5rem; border-radius: 5px;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>وقت الطبخ</label>
                        <input type="text" name="cooking_time" value="<?php echo htmlspecialchars($recipe['cooking_time']); ?>" placeholder="مثال: 30 دقيقة">
                    </div>
                    <div class="form-group">
                        <label>الصعوبة</label>
                        <select name="difficulty">
                            <option value="">اختر...</option>
                            <option value="سهل" <?php echo $recipe['difficulty'] === 'سهل' ? 'selected' : ''; ?>>سهل</option>
                            <option value="متوسط" <?php echo $recipe['difficulty'] === 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                            <option value="صعب" <?php echo $recipe['difficulty'] === 'صعب' ? 'selected' : ''; ?>>صعب</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">حفظ التعديلات</button>
            </form>
        </div>
    </div>
</body>
</html>

