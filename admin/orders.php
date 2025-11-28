<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

$conn = getDBConnection();

// تحديث حالة الطلب
if (isset($_GET['update_status'])) {
    $orderId = intval($_GET['update_status']);
    $status = $_GET['status'];
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    $stmt->close();
    redirect('admin/orders.php');
}

// جلب جميع الطلبات
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
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
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 5px;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>🌶️ إدارة الطلبات</h2>
                <div>
                    <a href="index.php">لوحة التحكم</a>
                    <a href="../index.php">الموقع</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2 style="margin-bottom: 2rem;">قائمة الطلبات</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>البريد</th>
                    <th>الهاتف</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_phone']); ?></td>
                        <td><?php echo formatPrice($order['total_price']); ?></td>
                        <td>
                            <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)" 
                                    style="padding: 0.5rem; border: 2px solid #e5e7eb; border-radius: 5px;">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>معلق</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>قيد المعالجة</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>تم الشحن</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>تم التسليم</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
                            </select>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-small">عرض التفاصيل</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function updateStatus(orderId, status) {
            if (confirm('هل تريد تحديث حالة الطلب؟')) {
                window.location.href = 'orders.php?update_status=' + orderId + '&status=' + status;
            }
        }
    </script>
</body>
</html>

