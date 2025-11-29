-- قاعدة بيانات موقع التوابل والبهارات
CREATE DATABASE IF NOT EXISTS tawabil_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tawabil_db;

-- جدول المنتجات
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    weight VARCHAR(50) NOT NULL,
    description TEXT,
    description_ar TEXT,
    category ENUM('spices', 'herbs', 'mixes', 'ground', 'whole') NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    best_seller BOOLEAN DEFAULT FALSE,
    promo BOOLEAN DEFAULT FALSE,
    promo_price DECIMAL(10, 2) NULL,
    origin_country VARCHAR(100),
    ingredients TEXT,
    benefits TEXT,
    usage_method TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول التقييمات
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_phone VARCHAR(50) NOT NULL,
    user_address TEXT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول عناصر الطلب
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الوصفات
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_ar VARCHAR(255) NOT NULL,
    description TEXT,
    description_ar TEXT,
    image VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    ingredients_ar TEXT NOT NULL,
    steps TEXT NOT NULL,
    steps_ar TEXT NOT NULL,
    spices_used TEXT,
    cooking_time VARCHAR(50),
    difficulty VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المسؤولين
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول رسائل الاتصال
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج بيانات تجريبية للمنتجات
INSERT INTO products (name, name_ar, price, weight, description, description_ar, category, image, stock, rating, best_seller, promo, origin_country, ingredients, benefits, usage_method) VALUES
('Cumin', 'كمون', 25.00, '100g', 'Premium quality cumin seeds', 'كمون عالي الجودة', 'spices', 'images/products/cumin.jpg', 50, 4.5, TRUE, FALSE, 'Morocco', 'Cumin seeds', 'Aids digestion, rich in iron', 'Add to tagines and stews'),
('Paprika', 'بابريكا', 30.00, '100g', 'Sweet paprika powder', 'بابريكا حلوة مطحونة', 'ground', 'images/products/paprika.jpg', 40, 4.7, TRUE, TRUE, 'Spain', 'Paprika powder', 'Rich in antioxidants', 'Use in marinades and rubs'),
('Turmeric', 'كركم', 28.00, '100g', 'Organic turmeric powder', 'كركم عضوي مطحون', 'ground', 'images/products/turmeric.jpg', 60, 4.6, FALSE, FALSE, 'India', 'Turmeric root', 'Anti-inflammatory properties', 'Add to curries and rice'),
('Ras el Hanout', 'راس الحانوت', 45.00, '100g', 'Traditional Moroccan spice blend', 'خليط التوابل المغربي التقليدي', 'mixes', 'images/products/ras_el_hanout.jpg', 35, 4.8, TRUE, FALSE, 'Morocco', '20+ spices blend', 'Complex flavor profile', 'Essential for tagines'),
('Cinnamon', 'قرفة', 35.00, '100g', 'Ceylon cinnamon sticks', 'عصي قرفة سيلان', 'whole', 'images/products/cinnamon.jpg', 45, 4.4, FALSE, TRUE, 'Sri Lanka', 'Cinnamon bark', 'Regulates blood sugar', 'Use in desserts and teas'),
('Ginger', 'زنجبيل', 32.00, '100g', 'Fresh dried ginger', 'زنجبيل مجفف طازج', 'herbs', 'images/products/ginger.jpg', 55, 4.5, FALSE, FALSE, 'China', 'Ginger root', 'Nausea relief, anti-inflammatory', 'Add to teas and dishes'),
('Black Pepper', 'فلفل أسود', 27.00, '100g', 'Whole black peppercorns', 'حبات فلفل أسود كاملة', 'whole', 'images/products/black_pepper.jpg', 70, 4.3, TRUE, FALSE, 'India', 'Peppercorns', 'Enhances nutrient absorption', 'Grind fresh for best flavor'),
('Coriander', 'كزبرة', 24.00, '100g', 'Coriander seeds', 'بذور كزبرة', 'spices', 'images/products/coriander.jpg', 50, 4.2, FALSE, FALSE, 'Morocco', 'Coriander seeds', 'Digestive aid', 'Use in spice blends');

-- إدراج بيانات تجريبية للوصفات
INSERT INTO recipes (title, title_ar, description, description_ar, image, ingredients, ingredients_ar, steps, steps_ar, spices_used, cooking_time, difficulty) VALUES
('Moroccan Tagine', 'الطاجين المغربي', 'Traditional Moroccan slow-cooked dish', 'طبق مغربي تقليدي مطبوخ ببطء', 'images/recipes/tagine.jpg', 'Lamb, onions, tomatoes, chickpeas', 'لحم ضأن، بصل، طماطم، حمص', '1. Brown the meat\n2. Add vegetables\n3. Season with spices\n4. Cook slowly', '1. قم بتحمير اللحم\n2. أضف الخضار\n3. تتبيل بالتوابل\n4. اطبخ ببطء', 'Ras el Hanout, Cumin, Turmeric', '2 hours', 'Medium'),
('Spiced Rice', 'أرز بالتوابل', 'Aromatic spiced rice dish', 'طبق أرز معطر بالتوابل', 'images/recipes/spiced_rice.jpg', 'Rice, onions, garlic, broth', 'أرز، بصل، ثوم، مرق', '1. Sauté onions\n2. Add rice and spices\n3. Cook with broth', '1. قم بقلي البصل\n2. أضف الأرز والتوابل\n3. اطبخ بالمرق', 'Cumin, Cinnamon, Turmeric', '30 minutes', 'Easy');

-- إدراج بيانات تجريبية للتقييمات
INSERT INTO reviews (product_id, user_name, rating, comment) VALUES
(1, 'أحمد محمد', 5, 'كمون ممتاز الجودة، أنصح به بشدة'),
(2, 'فاطمة علي', 5, 'بابريكا رائعة، لونها جميل ونكهتها قوية'),
(4, 'محمد حسن', 5, 'راس الحانوت أصيل ومميز، يجعل الطاجين لذيذ جداً');

-- إدراج مسؤول افتراضي (كلمة المرور: admin123)
INSERT INTO admin (username, password, email) VALUES
('admin', '$2y$10$bLHf9vrl7XV8ux.diaiVke6QyyeASFyUJsFMoGrEZwwLyAuMoOKTa', 'admin@tawabil.com');

