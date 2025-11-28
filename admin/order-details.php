<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

if (!isset($_GET['id'])) {
    redirect('admin/orders.php');
}

$conn = getDBConnection();
$orderId = intval($_GET['id']);

// جلب بيانات الطلب
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    redirect('admin/orders.php');
}

// جلب عناصر الطلب
$sql = "SELECT oi.*, p.name_ar, p.image FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$orderItems = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطلب</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d97706;
            --secondary-color: #92400e;
            --accent-color: #f59e0b;
            --dark-color: #451a03;
            --light-color: #fef3c7;
            --text-color: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        body {
            font-family: 'Cairo', 'Arial', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        
        .admin-header h2 {
            margin: 0;
            font-size: 1.75rem;
        }
        
        .admin-nav {
            display: flex;
            gap: 1rem;
        }
        
        .admin-nav a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .admin-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .order-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-processing { background-color: #dbeafe; color: #1e40af; }
        .status-shipped { background-color: #e0e7ff; color: #3730a3; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .info-card h3 {
            margin: 0 0 1rem 0;
            color: var(--primary-color);
            font-size: 1.25rem;
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 0.5rem;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-icon {
            font-size: 1.25rem;
            color: var(--primary-color);
            min-width: 25px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: var(--text-light);
            line-height: 1.6;
        }
        
        .order-items-card {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .order-items-card h3 {
            margin: 0 0 1.5rem 0;
            color: var(--primary-color);
            font-size: 1.5rem;
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 0.75rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.3s ease;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item:hover {
            background-color: #f9fafb;
        }
        
        .order-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        
        .order-item-info {
            flex: 1;
        }
        
        .order-item-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .order-item-details {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .order-item-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: left;
            min-width: 120px;
        }
        
        .order-total {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        
        .order-total-label {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }
        
        .order-total-amount {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .order-item {
                flex-direction: column;
                text-align: center;
            }
            
            .order-item-price {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>🌶️ تفاصيل الطلب #<?php echo $order['id']; ?></h2>
                <div>
                    <a href="orders.php">العودة</a>
                    <a href="../index.php">الموقع</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- رأس الطلب -->
        <div class="order-header">
            <div>
                <div class="order-number">طلب رقم #<?php echo $order['id']; ?></div>
                <div style="color: var(--text-light); margin-top: 0.5rem;">
                    <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?>
                </div>
            </div>
            <div>
                <?php
                $statuses = [
                    'pending' => ['text' => 'معلق', 'class' => 'status-pending'],
                    'processing' => ['text' => 'قيد المعالجة', 'class' => 'status-processing'],
                    'shipped' => ['text' => 'تم الشحن', 'class' => 'status-shipped'],
                    'delivered' => ['text' => 'تم التسليم', 'class' => 'status-delivered'],
                    'cancelled' => ['text' => 'ملغي', 'class' => 'status-cancelled']
                ];
                $status = $statuses[$order['status']] ?? ['text' => $order['status'], 'class' => 'status-pending'];
                ?>
                <span class="status-badge <?php echo $status['class']; ?>">
                    <?php echo $status['text']; ?>
                </span>
            </div>
        </div>
        
        <!-- معلومات العميل والطلب -->
        <div class="info-grid">
            <div class="info-card">
                <h3>👤 معلومات العميل</h3>
                <div class="info-item">
                    <span class="info-icon">👤</span>
                    <div>
                        <div class="info-label">الاسم الكامل</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['user_name']); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📧</span>
                    <div>
                        <div class="info-label">البريد الإلكتروني</div>
                        <div class="info-value">
                            <a href="mailto:<?php echo htmlspecialchars($order['user_email']); ?>" style="color: var(--primary-color); text-decoration: none;">
                                <?php echo htmlspecialchars($order['user_email']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📞</span>
                    <div>
                        <div class="info-label">رقم الهاتف</div>
                        <div class="info-value">
                            <a href="tel:<?php echo htmlspecialchars($order['user_phone']); ?>" style="color: var(--primary-color); text-decoration: none;">
                                <?php echo htmlspecialchars($order['user_phone']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <h3>📍 عنوان التوصيل</h3>
                <div class="info-item">
                    <span class="info-icon">📍</span>
                    <div>
                        <div class="info-value" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($order['user_address'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <h3>📋 معلومات الطلب</h3>
                <div class="info-item">
                    <span class="info-icon">🆔</span>
                    <div>
                        <div class="info-label">رقم الطلب</div>
                        <div class="info-value">#<?php echo $order['id']; ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📅</span>
                    <div>
                        <div class="info-label">تاريخ الطلب</div>
                        <div class="info-value"><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">📦</span>
                    <div>
                        <div class="info-label">عدد المنتجات</div>
                        <div class="info-value"><?php echo count($orderItems); ?> منتج</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- عناصر الطلب -->
        <div class="order-items-card">
            <h3>🛒 عناصر الطلب</h3>
            <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <img src="../<?php echo $item['image']; ?>" 
                         alt="<?php echo htmlspecialchars($item['name_ar']); ?>" 
                         class="order-item-image"
                         onerror="this.src='../images/placeholder.jpg'">
                    <div class="order-item-info">
                        <div class="order-item-name"><?php echo htmlspecialchars($item['name_ar']); ?></div>
                        <div class="order-item-details">
                            الكمية: <strong><?php echo $item['quantity']; ?></strong> × 
                            السعر: <strong><?php echo formatPrice($item['price']); ?></strong>
                        </div>
                    </div>
                    <div class="order-item-price">
                        <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- الإجمالي -->
        <div class="order-total">
            <div class="order-total-label">إجمالي الطلب</div>
            <div class="order-total-amount"><?php echo formatPrice($order['total_price']); ?></div>
        </div>
        
        <!-- أزرار الإجراءات -->
        <div class="action-buttons">
            <?php if ($order['status'] !== 'processing'): ?>
            <a href="orders.php?update_status=<?php echo $order['id']; ?>&status=processing" 
               class="btn btn-primary"
               onclick="return confirm('هل تريد تحديث حالة الطلب إلى قيد المعالجة؟')">
                ✅ قيد المعالجة
            </a>
            <?php endif; ?>
            
            <?php if ($order['status'] !== 'shipped'): ?>
            <a href="orders.php?update_status=<?php echo $order['id']; ?>&status=shipped" 
               class="btn btn-primary"
               onclick="return confirm('هل تريد تحديث حالة الطلب إلى تم الشحن؟')">
                🚚 تم الشحن
            </a>
            <?php endif; ?>
            
            <?php if ($order['status'] !== 'delivered'): ?>
            <a href="orders.php?update_status=<?php echo $order['id']; ?>&status=delivered" 
               class="btn btn-primary"
               onclick="return confirm('هل تريد تحديث حالة الطلب إلى تم التسليم؟')">
                ✓ تم التسليم
            </a>
            <?php endif; ?>
            
            <a href="orders.php" class="btn btn-secondary">
                ← العودة إلى الطلبات
            </a>
        </div>
    </div>
    
    <script>
        // تحسين تجربة المستخدم
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثيرات عند التمرير
            const cards = document.querySelectorAll('.info-card, .order-items-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            });
            
            setTimeout(() => {
                cards.forEach(card => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, 100);
        });
    </script>
</body>
</html>

