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

function updateProductsGrid(products) {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;
    
    grid.innerHTML = products.map(product => `
        <div class="product-card">
            ${product.promo ? '<span class="product-badge">عرض خاص</span>' : ''}
            <img src="${product.image}" alt="${product.name_ar}" class="product-image" onerror="this.src='images/placeholder.jpg'">
            <div class="product-info">
                <h3 class="product-name">${product.name_ar}</h3>
                <p class="product-weight">${product.weight}</p>
                <p class="product-description">${product.description_ar || product.description}</p>
                <div class="product-footer">
                    <div>
                        ${product.promo && product.promo_price ? 
                            `<span class="product-price">${product.promo_price} د.م.</span>
                             <span class="product-price-old">${product.price} د.م.</span>` :
                            `<span class="product-price">${product.price} د.م.</span>`
                        }
                    </div>
                    <button class="add-to-cart" onclick="addToCart(${product.id}, '${product.name_ar}', ${product.promo && product.promo_price ? product.promo_price : product.price})">
                        أضف للسلة
                    </button>
                </div>
            </div>
        </div>
    `).join('');
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

