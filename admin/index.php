<?php
require_once '../config/config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

$conn = getDBConnection();

// إحصائيات
$stats = [];

// عدد المنتجات
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// عدد الطلبات
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// عدد الطلبات المعلقة
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// إجمالي المبيعات
$result = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status != 'cancelled'");
$stats['total_sales'] = $result->fetch_assoc()['total'] ?: 0;

// عدد الوصفات
$result = $conn->query("SELECT COUNT(*) as count FROM recipes");
$stats['recipes'] = $result->fetch_assoc()['count'];

// عدد رسائل الاتصال الجديدة
$result = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'new'");
$stats['new_contacts'] = $result->fetch_assoc()['count'];

// آخر الطلبات
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background-color: var(--white);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: var(--text-light);
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
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>🌶️ لوحة التحكم</h2>
                <div>
                    <a href="products.php">المنتجات</a>
                    <a href="orders.php">الطلبات</a>
                    <a href="recipes.php">الوصفات</a>
                    <a href="contacts.php">رسائل الاتصال</a>
                    <a href="../index.php">الموقع</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2 style="margin-bottom: 2rem;">الإحصائيات</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['products']; ?></div>
                <div class="stat-label">المنتجات</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['orders']; ?></div>
                <div class="stat-label">الطلبات</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                <div class="stat-label">طلبات معلقة</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['total_sales'], 2); ?> د.م.</div>
                <div class="stat-label">إجمالي المبيعات</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['recipes']; ?></div>
                <div class="stat-label">الوصفات</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #1e40af;"><?php echo $stats['new_contacts']; ?></div>
                <div class="stat-label">رسائل جديدة</div>
            </div>
        </div>
        
        <h2 style="margin-bottom: 1rem;">آخر الطلبات</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo formatPrice($order['total_price']); ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 5px; background-color: 
                                <?php 
                                switch($order['status']) {
                                    case 'pending': echo '#fef3c7'; break;
                                    case 'processing': echo '#dbeafe'; break;
                                    case 'shipped': echo '#e0e7ff'; break;
                                    case 'delivered': echo '#d1fae5'; break;
                                    default: echo '#fee2e2';
                                }
                                ?>;">
                                <?php
                                $statuses = [
                                    'pending' => 'معلق',
                                    'processing' => 'قيد المعالجة',
                                    'shipped' => 'تم الشحن',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'ملغي'
                                ];
                                echo $statuses[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-small">عرض</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

