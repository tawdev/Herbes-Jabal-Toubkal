<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('login.php');
}

$conn = getDBConnection();

// تحديث حالة الرسالة
if (isset($_GET['update_status'])) {
    $contactId = intval($_GET['update_status']);
    $status = $_GET['status'];
    $sql = "UPDATE contacts SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $contactId);
    $stmt->execute();
    $stmt->close();
    redirect('contacts.php');
}

// حذف رسالة
if (isset($_GET['delete'])) {
    $contactId = intval($_GET['delete']);
    $sql = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
    $stmt->close();
    redirect('contacts.php');
}

// حفظ ملاحظات المسؤول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    $contactId = intval($_POST['contact_id']);
    $notes = $_POST['admin_notes'] ?? '';
    $sql = "UPDATE contacts SET admin_notes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $notes, $contactId);
    $stmt->execute();
    $stmt->close();
    redirect('contacts.php');
}

// جلب جميع الرسائل
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);
$contacts = $result->fetch_all(MYSQLI_ASSOC);

// إحصائيات
$stats = [
    'total' => count($contacts),
    'new' => 0,
    'read' => 0,
    'replied' => 0
];

foreach ($contacts as $contact) {
    $stats[$contact['status']]++;
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة رسائل الاتصال</title>
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
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background-color: var(--white);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
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
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-new { background-color: #dbeafe; color: #1e40af; }
        .status-read { background-color: #fef3c7; color: #92400e; }
        .status-replied { background-color: #d1fae5; color: #065f46; }
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .contact-message {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>🌶️ إدارة رسائل الاتصال</h2>
                <div>
                    <a href="index.php">لوحة التحكم</a>
                    <a href="../index.php">الموقع</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2 style="margin-bottom: 2rem;">رسائل الاتصال</h2>
        
        <!-- الإحصائيات -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">إجمالي الرسائل</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #1e40af;"><?php echo $stats['new']; ?></div>
                <div class="stat-label">جديدة</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #92400e;"><?php echo $stats['read']; ?></div>
                <div class="stat-label">مقروءة</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #065f46;"><?php echo $stats['replied']; ?></div>
                <div class="stat-label">تم الرد</div>
            </div>
        </div>
        
        <!-- جدول الرسائل -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الرسالة</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-light);">
                            لا توجد رسائل اتصال
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['name']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" style="color: var(--primary-color);">
                                    <?php echo htmlspecialchars($contact['email']); ?>
                                </a>
                            </td>
                            <td class="contact-message" title="<?php echo htmlspecialchars($contact['message']); ?>">
                                <?php echo htmlspecialchars(mb_substr($contact['message'], 0, 50)); ?>...
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $contact['status']; ?>">
                                    <?php
                                    $statuses = [
                                        'new' => 'جديدة',
                                        'read' => 'مقروءة',
                                        'replied' => 'تم الرد'
                                    ];
                                    echo $statuses[$contact['status']] ?? $contact['status'];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($contact['created_at'])); ?></td>
                            <td>
                                <a href="contact-details.php?id=<?php echo $contact['id']; ?>" class="btn btn-small">عرض</a>
                                <a href="contacts.php?delete=<?php echo $contact['id']; ?>" 
                                   class="btn btn-small" 
                                   style="background-color: #ef4444;"
                                   onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

