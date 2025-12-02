<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('login.php');
}

$conn = getDBConnection();

// معالجة إضافة المنتج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $name_ar = $_POST['name_ar'];
    $price = floatval($_POST['price']);
    $weight = $_POST['weight'];
    $description = $_POST['description'];
    $description_ar = $_POST['description_ar'];
    $category = $_POST['category'];
    $stock = intval($_POST['stock']);
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    $promo = isset($_POST['promo']) ? 1 : 0;
    $promo_price = !empty($_POST['promo_price']) ? floatval($_POST['promo_price']) : null;
    $origin_country = $_POST['origin_country'] ?? '';
    $ingredients = $_POST['ingredients'] ?? '';
    $benefits = $_POST['benefits'] ?? '';
    $usage_method = $_POST['usage_method'] ?? '';
    
    // معالجة رفع الصورة
    $image = 'images/products/placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'images/products/' . $fileName;
        }
    }
    
    $sql = "INSERT INTO products (name, name_ar, price, weight, description, description_ar, category, image, stock, best_seller, promo, promo_price, origin_country, ingredients, benefits, usage_method) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssssiiidssss", $name, $name_ar, $price, $weight, $description, $description_ar, $category, $image, $stock, $best_seller, $promo, $promo_price, $origin_country, $ingredients, $benefits, $usage_method);
    $stmt->execute();
    $stmt->close();
    closeDBConnection($conn);
    
    redirect('products.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج</title>
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
            transition: var(--transition);
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
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>🌶️ إضافة منتج جديد</h2>
                <div>
                    <a href="products.php">العودة</a>
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
                        <label>الاسم (إنجليزي) *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>الاسم (عربي) *</label>
                        <input type="text" name="name_ar" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>السعر (درهم) *</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>الوزن *</label>
                        <input type="text" name="weight" placeholder="100g" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>التصنيف *</label>
                        <select name="category" required>
                            <option value="spices">توابل</option>
                            <option value="herbs">أعشاب</option>
                            <option value="mixes">خلطات</option>
                            <option value="ground">مطحونة</option>
                            <option value="whole">كاملة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>المخزون *</label>
                        <input type="number" name="stock" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>الوصف (إنجليزي)</label>
                    <textarea name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>الوصف (عربي)</label>
                    <textarea name="description_ar"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>بلد المنشأ</label>
                        <input type="text" name="origin_country">
                    </div>
                    <div class="form-group">
                        <label>الصورة *</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>المكونات</label>
                    <textarea name="ingredients"></textarea>
                </div>
                
                <div class="form-group">
                    <label>الفوائد</label>
                    <textarea name="benefits"></textarea>
                </div>
                
                <div class="form-group">
                    <label>طريقة الاستخدام</label>
                    <textarea name="usage_method"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="best_seller" id="best_seller">
                            <label for="best_seller" style="margin: 0;">أفضل مبيع</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="promo" id="promo" onchange="togglePromoPrice()">
                            <label for="promo" style="margin: 0;">عرض خاص</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" id="promo_price_group" style="display: none;">
                    <label>سعر العرض الخاص</label>
                    <input type="number" name="promo_price" step="0.01">
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">إضافة المنتج</button>
            </form>
        </div>
    </div>
    
    <script>
        function togglePromoPrice() {
            const promoCheckbox = document.getElementById('promo');
            const promoPriceGroup = document.getElementById('promo_price_group');
            promoPriceGroup.style.display = promoCheckbox.checked ? 'block' : 'none';
        }
    </script>
</body>
</html>

