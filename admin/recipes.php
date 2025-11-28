<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

$conn = getDBConnection();

// حذف وصفة
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('admin/recipes.php');
}

// جلب جميع الوصفات
$sql = "SELECT * FROM recipes ORDER BY id DESC";
$result = $conn->query($sql);
$recipes = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الوصفات</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }
        .admin-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .recipe-image-small {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>🌶️ إدارة الوصفات</h2>
                <div>
                    <a href="index.php">لوحة التحكم</a>
                    <a href="recipe-add.php">إضافة وصفة</a>
                    <a href="../index.php">الموقع</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>قائمة الوصفات</h2>
            <a href="recipe-add.php" class="btn">+ إضافة وصفة جديدة</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>العنوان</th>
                    <th>وقت الطبخ</th>
                    <th>الصعوبة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recipes as $recipe): ?>
                    <tr>
                        <td>
                            <img src="../<?php echo $recipe['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($recipe['title_ar']); ?>" 
                                 class="recipe-image-small"
                                 onerror="this.src='../images/placeholder.jpg'">
                        </td>
                        <td><?php echo htmlspecialchars($recipe['title_ar']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['cooking_time'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($recipe['difficulty'] ?: '-'); ?></td>
                        <td>
                            <a href="recipe-edit.php?id=<?php echo $recipe['id']; ?>" class="btn btn-small">تعديل</a>
                            <a href="recipes.php?delete=<?php echo $recipe['id']; ?>" 
                               class="btn btn-small" 
                               style="background-color: #ef4444;"
                               onclick="return confirm('هل أنت متأكد من حذف هذه الوصفة؟')">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

