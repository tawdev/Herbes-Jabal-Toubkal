<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

$conn = getDBConnection();

// حذف منتج
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('admin/products.php');
}

// جلب جميع المنتجات
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات</title>
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
            transition: var(--transition);
        }
        .admin-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
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
        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>🌶️ إدارة المنتجات</h2>
                <div>
                    <a href="index.php">لوحة التحكم</a>
                    <a href="product-add.php">إضافة منتج</a>
                    <a href="../index.php">الموقع</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>قائمة المنتجات</h2>
            <a href="product-add.php" class="btn">+ إضافة منتج جديد</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>السعر</th>
                    <th>التصنيف</th>
                    <th>المخزون</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="../<?php echo $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name_ar']); ?>" 
                                 class="product-image-small"
                                 onerror="this.src='../images/placeholder.jpg'">
                        </td>
                        <td><?php echo htmlspecialchars($product['name_ar']); ?></td>
                        <td><?php echo formatPrice($product['price']); ?></td>
                        <td>
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
                        </td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-small">تعديل</a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" 
                               class="btn btn-small" 
                               style="background-color: #ef4444;"
                               onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

