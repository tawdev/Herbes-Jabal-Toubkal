<?php
// ملاحظة: config.php يتم استدعاؤه في الصفحات قبل header.php
// لا حاجة لاستدعائه هنا
if (!defined('SITE_NAME')) {
    die('خطأ: يجب استدعاء config.php قبل header.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <h1>Herbes Jabal Toubkal</h1>
                    </a>
                </div>
                
                <nav class="nav-menu" id="navMenu">
                    <a href="<?php echo BASE_URL; ?>index.php">الرئيسية</a>
                    <a href="<?php echo BASE_URL; ?>shop.php">المتجر</a>
                    <a href="<?php echo BASE_URL; ?>recipes.php">الوصفات</a>
                    <a href="<?php echo BASE_URL; ?>index.php#about">من نحن</a>
                    <a href="<?php echo BASE_URL; ?>index.php#contact">اتصل بنا</a>
                </nav>
                
                <div class="header-actions">
                    <button class="search-btn" id="searchBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                    <a href="<?php echo BASE_URL; ?>cart.php" class="cart-btn" id="cartBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="cart-count" id="cartCount"><?php echo count($_SESSION['cart']); ?></span>
                    </a>
                    <button class="menu-toggle" id="menuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- شريط البحث -->
        <div class="search-bar" id="searchBar">
            <div class="container">
                <form action="<?php echo BASE_URL; ?>shop.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="ابحث عن التوابل والبهارات..." class="search-input">
                    <button type="submit" class="search-submit">بحث</button>
                </form>
            </div>
        </div>
    </header>

