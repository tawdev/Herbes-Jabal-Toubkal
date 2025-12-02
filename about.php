<?php
$pageTitle = "من نحن";
require_once 'config/config.php';
require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="section-title" style="margin: 0;">من نحن - لماذا تختارنا؟</h2>
            <a href="index.php" class="btn" style="background-color: #6b7280; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                العودة إلى الرئيسية
            </a>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">جودة عالية</h3>
                <p class="feature-description">نختار أجود أنواع التوابل من مصادر موثوقة لضمان الجودة والنكهة الأصيلة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">توصيل سريع</h3>
                <p class="feature-description">خدمة توصيل سريعة وآمنة لجميع أنحاء المغرب خلال 24-48 ساعة</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">أسعار مناسبة</h3>
                <p class="feature-description">أفضل الأسعار في السوق مع عروض وخصومات حصرية على منتجاتنا</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3 class="feature-title">طبيعي 100%</h3>
                <p class="feature-description">جميع منتجاتنا طبيعية بدون إضافات أو مواد حافظة</p>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

