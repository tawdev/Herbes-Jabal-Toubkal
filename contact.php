<?php
$pageTitle = "اتصل بنا";
require_once 'config/config.php';
require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="section-title" style="margin: 0;">اتصل بنا</h2>
            <a href="index.php" class="btn" style="background-color: #6b7280; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                العودة إلى الرئيسية
            </a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <div class="feature-card">
                <div class="feature-icon">📧</div>
                <h3 class="feature-title">البريد الإلكتروني</h3>
                <p class="feature-description">
                    <a href="mailto:contact@herbesjabaltoubkal.com" style="color: var(--primary-color); text-decoration: none;">
                        contact@herbesjabaltoubkal.com
                    </a>
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📞</div>
                <h3 class="feature-title">رقم الهاتف</h3>
                <p class="feature-description">
                    <a href="tel:+212674862173" style="color: var(--primary-color); text-decoration: none;">
                        +212 674-862173
                    </a>
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📍</div>
                <h3 class="feature-title">العنوان</h3>
                <p class="feature-description">
                    N, TAW10, lot Iguder, 48 AV Alla El Fassi<br>
                    Marrakech 40000, Morocco
                </p>
            </div>
        </div>
        
        <!-- نموذج الاتصال -->
        <div style="max-width: 600px; margin: 3rem auto 0; background-color: var(--white); border-radius: 10px; box-shadow: var(--shadow); padding: 2rem;">
            <h3 style="text-align: center; margin-bottom: 1.5rem; color: var(--dark-color);">أرسل لنا رسالة</h3>
            <form id="contactForm" method="POST" action="api/contact.php">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الاسم *</label>
                    <input type="text" name="name" required 
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">البريد الإلكتروني *</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الرسالة *</label>
                    <textarea name="message" required rows="5" 
                              style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 5px; font-size: 1rem; resize: vertical;"></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%;">
                    إرسال الرسالة
                </button>
                <div id="contactMessage" style="margin-top: 1rem; text-align: center; display: none;"></div>
            </form>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

