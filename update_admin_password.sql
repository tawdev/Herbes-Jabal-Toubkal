-- تحديث كلمة مرور المسؤول
-- كلمة المرور: admin123

UPDATE admin SET password = '$2y$10$bLHf9vrl7XV8ux.diaiVke6QyyeASFyUJsFMoGrEZwwLyAuMoOKTa' WHERE username = 'admin';

-- أو إذا لم يكن هناك مسؤول، قم بإنشاء واحد جديد:
-- INSERT INTO admin (username, password, email) VALUES
-- ('admin', '$2y$10$bLHf9vrl7XV8ux.diaiVke6QyyeASFyUJsFMoGrEZwwLyAuMoOKTa', 'admin@tawabil.com');

