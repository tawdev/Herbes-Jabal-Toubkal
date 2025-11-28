<?php
/**
 * ملف الإعداد الأولي
 * استخدم هذا الملف لإنشاء hash جديد لكلمة المرور
 */

// إنشاء hash جديد لكلمة المرور
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "كلمة المرور: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\n";
echo "استخدم هذا الـ hash في قاعدة البيانات:\n";
echo "UPDATE admin SET password = '" . $hash . "' WHERE username = 'admin';\n";
?>

