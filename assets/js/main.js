// القائمة المتحركة
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.getElementById('navMenu');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// شريط البحث
const searchBtn = document.getElementById('searchBtn');
const searchBar = document.getElementById('searchBar');

if (searchBtn && searchBar) {
    searchBtn.addEventListener('click', () => {
        searchBar.classList.toggle('active');
    });
}

// إضافة إلى السلة
function addToCart(productId, productName, price) {
    // إرسال طلب AJAX لإضافة المنتج للسلة
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث عداد السلة
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            
            // إظهار رسالة نجاح
            showNotification('تم إضافة المنتج إلى السلة بنجاح!', 'success');
        } else {
            showNotification('حدث خطأ أثناء إضافة المنتج', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء إضافة المنتج', 'error');
    });
}

// دالة إظهار الإشعارات
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// فلترة وترتيب المنتجات
function filterProducts() {
    const category = document.getElementById('categoryFilter')?.value || 'all';
    const sortBy = document.getElementById('sortFilter')?.value || 'default';
    
    // إرسال طلب AJAX
    fetch(`api/get_products.php?category=${category}&sort=${sortBy}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductsGrid(data.products);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// دالة لتنسيق السعر (مطابقة لتنسيق PHP)
function formatPriceJS(price) {
    // تحويل السعر إلى نص
    let priceStr = String(price);
    
    // إزالة أي أرقام غريبة تظهر قبل أو بعد السعر (مثل 262145)
    priceStr = priceStr.replace(/^\d+\s+/, ''); // إزالة الأرقام في البداية
    priceStr = priceStr.replace(/\s+\d+/, ''); // إزالة الأرقام في النهاية
    priceStr = priceStr.replace(/\d{6,}/, ''); // إزالة أي أرقام طويلة (6+ أرقام)
    
    // استخراج الرقم الفعلي فقط
    const match = priceStr.match(/[\d]+\.?[\d]*/);
    if (match) {
        priceStr = match[0];
    }
    
    // تحويل إلى رقم وتنسيقه
    const numPrice = parseFloat(priceStr) || 0;
    const formatted = numPrice.toFixed(2);
    
    return formatted + ' DH';
}

function updateProductsGrid(products) {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;
    
    grid.innerHTML = products.map(product => {
        // تنظيف الأسعار
        const displayPrice = formatPriceJS(product.price);
        const displayPromoPrice = product.promo_price ? formatPriceJS(product.promo_price) : null;
        
        return `
        <div class="product-card">
            ${product.promo ? '<span class="product-badge">عرض خاص</span>' : ''}
            <img src="${product.image}" alt="${product.name_ar}" class="product-image" onerror="this.src='images/placeholder.jpg'">
            <div class="product-info">
                <h3 class="product-name">${product.name_ar}</h3>
                <p class="product-weight">${product.weight}</p>
                <p class="product-description">${product.description_ar || product.description}</p>
                <div class="product-footer">
                    <div>
                        ${product.promo && displayPromoPrice ? 
                            `<span class="product-price">${displayPromoPrice}</span>
                             <span class="product-price-old">${displayPrice}</span>` :
                            `<span class="product-price">${displayPrice}</span>`
                        }
                    </div>
                    <button class="add-to-cart" onclick="addToCart(${product.id}, '${product.name_ar}', ${parseFloat(displayPrice.replace(' DH', ''))})">
                        أضف للسلة
                    </button>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

// تكبير صورة المنتج
function zoomImage(img) {
    if (img.style.transform === 'scale(2)') {
        img.style.transform = 'scale(1)';
        img.style.cursor = 'zoom-in';
    } else {
        img.style.transform = 'scale(2)';
        img.style.cursor = 'zoom-out';
    }
}

// إضافة تأثيرات التمرير
window.addEventListener('scroll', () => {
    const header = document.querySelector('.header');
    if (window.scrollY > 100) {
        header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.2)';
    } else {
        header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    }
});

// Smooth scroll للروابط الداخلية
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// معالجة نموذج الاتصال
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('contactMessage');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // تعطيل الزر أثناء الإرسال
        submitBtn.disabled = true;
        submitBtn.textContent = 'جاري الإرسال...';
        messageDiv.style.display = 'none';
        
        fetch('api/contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.style.display = 'block';
            if (data.success) {
                messageDiv.style.color = '#10b981';
                messageDiv.textContent = data.message;
                contactForm.reset();
            } else {
                messageDiv.style.color = '#ef4444';
                messageDiv.textContent = data.message || 'حدث خطأ أثناء إرسال الرسالة';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.style.display = 'block';
            messageDiv.style.color = '#ef4444';
            messageDiv.textContent = 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'إرسال الرسالة';
        });
    });
}

// إضافة أنيميشن للبطاقات عند التمرير
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.product-card, .recipe-card, .feature-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(card);
});

