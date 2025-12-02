<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect('contacts.php');

$conn = getDBConnection();
$contactId = intval($_GET['id']);

// جلب بيانات الرسالة
$sql = "SELECT * FROM contacts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contactId);
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();
$stmt->close();

if (!$contact) {
    redirect('contacts.php');
}

// تحديث الحالة إلى "مقروءة" إذا كانت جديدة
if ($contact['status'] === 'new') {
    $sql = "UPDATE contacts SET status = 'read' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
    $stmt->close();
    $contact['status'] = 'read';
}

// حفظ ملاحظات المسؤول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    $notes = $_POST['admin_notes'] ?? '';
    $status = $_POST['status'] ?? $contact['status'];
    
    $sql = "UPDATE contacts SET admin_notes = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $notes, $status, $contactId);
    $stmt->execute();
    $stmt->close();
    
    redirect('contact-details.php?id=' . $contactId);
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الرسالة</title>
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
        .contact-details {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .info-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        .info-value {
            color: var(--text-light);
            line-height: 1.8;
        }
        .message-box {
            background-color: var(--light-color);
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 1rem;
            line-height: 1.8;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
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
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>🌶️ تفاصيل الرسالة #<?php echo $contact['id']; ?></h2>
                <div>
                    <a href="contacts.php">العودة</a>
                    <a href="../index.php">الموقع</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="contact-details">
            <div class="info-item">
                <span class="info-label">الاسم:</span>
                <span class="info-value"><?php echo htmlspecialchars($contact['name']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">البريد الإلكتروني:</span>
                <span class="info-value">
                    <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" style="color: var(--primary-color);">
                        <?php echo htmlspecialchars($contact['email']); ?>
                    </a>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">الرسالة:</span>
                <div class="message-box">
                    <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                </div>
            </div>
            
            <div class="info-item">
                <span class="info-label">التاريخ:</span>
                <span class="info-value"><?php echo date('Y-m-d H:i', strtotime($contact['created_at'])); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">الحالة:</span>
                <span class="info-value">
                    <?php
                    $statuses = [
                        'new' => 'جديدة',
                        'read' => 'مقروءة',
                        'replied' => 'تم الرد'
                    ];
                    echo $statuses[$contact['status']] ?? $contact['status'];
                    ?>
                </span>
            </div>
        </div>
        
        <!-- ملاحظات المسؤول -->
        <div class="contact-details">
            <h3 style="margin-bottom: 1.5rem; color: var(--dark-color);">ملاحظات المسؤول</h3>
            <form method="POST" action="contact-details.php?id=<?php echo $contact['id']; ?>">
                <div class="form-group">
                    <label for="status">الحالة:</label>
                    <select name="status" id="status">
                        <option value="new" <?php echo $contact['status'] === 'new' ? 'selected' : ''; ?>>جديدة</option>
                        <option value="read" <?php echo $contact['status'] === 'read' ? 'selected' : ''; ?>>مقروءة</option>
                        <option value="replied" <?php echo $contact['status'] === 'replied' ? 'selected' : ''; ?>>تم الرد</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="admin_notes">الملاحظات:</label>
                    <textarea name="admin_notes" id="admin_notes" placeholder="أضف ملاحظاتك هنا..."><?php echo htmlspecialchars($contact['admin_notes'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" name="save_notes" class="btn">حفظ الملاحظات</button>
            </form>
        </div>
    </div>
</body>
</html>

