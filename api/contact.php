<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

// التحقق من البيانات
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'جميع الحقول مطلوبة']);
    exit;
}

// التحقق من صحة البريد الإلكتروني
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
    exit;
}

// تنظيف البيانات
$name = trim($name);
$email = trim($email);
$message = trim($message);

// حفظ الرسالة في قاعدة البيانات
$conn = getDBConnection();

$sql = "INSERT INTO contacts (name, email, message, status) VALUES (?, ?, ?, 'new')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    $contactId = $stmt->insert_id;
    $stmt->close();
    closeDBConnection($conn);
    
    // محاولة إرسال بريد إلكتروني (اختياري)
    $to = 'contact@herbesjabaltoubkal.com';
    $subject = 'رسالة جديدة من موقع Herbes Jabal Toubkal';
    $emailMessage = "اسم المرسل: $name\n";
    $emailMessage .= "البريد الإلكتروني: $email\n\n";
    $emailMessage .= "الرسالة:\n$message";
    
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // محاولة إرسال البريد (يتطلب إعداد mail server)
    @mail($to, $subject, $emailMessage, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.',
        'contact_id' => $contactId
    ]);
} else {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حفظ الرسالة. يرجى المحاولة مرة أخرى.'
    ]);
}
?>

