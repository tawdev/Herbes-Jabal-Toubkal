// تحديث المجموع تلقائياً عند تغيير الكمية
function updateItemTotal(productId, price) {
    const quantityInput = document.querySelector(`input[data-product-id="${productId}"]`);
    const itemTotalSpan = document.querySelector(`.item-total[data-product-id="${productId}"]`);
    const cartTotalSpan = document.getElementById('cart-total');
    
    if (!quantityInput || !itemTotalSpan || !cartTotalSpan) {
        return;
    }
    
    const quantity = parseInt(quantityInput.value) || 1;
    
    // تحديث SESSION عبر AJAX
    updateCartQuantityInSession(productId, quantity, price, itemTotalSpan, cartTotalSpan);
}

// تحديث الكمية في SESSION عبر AJAX
function updateCartQuantityInSession(productId, quantity, price, itemTotalSpan, cartTotalSpan) {
    fetch('api/update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث مجموع المنتج
            if (itemTotalSpan) {
                itemTotalSpan.textContent = formatPrice(data.item_total);
            }
            
            // تحديث الإجمالي الكلي
            if (cartTotalSpan) {
                cartTotalSpan.textContent = formatPrice(data.cart_total);
            }
        } else {
            console.error('Error updating cart:', data.message);
            // في حالة الخطأ، نحدث الواجهة فقط
            const itemTotal = price * quantity;
            if (itemTotalSpan) {
                itemTotalSpan.textContent = formatPrice(itemTotal);
            }
            updateCartTotal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // في حالة الخطأ، نحدث الواجهة فقط
        const itemTotal = price * quantity;
        if (itemTotalSpan) {
            itemTotalSpan.textContent = formatPrice(itemTotal);
        }
        updateCartTotal();
    });
}

// تحديث الإجمالي الكلي للسلة (للحساب المحلي فقط)
function updateCartTotal() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    let total = 0;
    
    quantityInputs.forEach(input => {
        const productId = input.getAttribute('data-product-id');
        const price = parseFloat(input.getAttribute('data-price'));
        const quantity = parseInt(input.value) || 1;
        total += price * quantity;
    });
    
    const cartTotalSpan = document.getElementById('cart-total');
    if (cartTotalSpan) {
        cartTotalSpan.textContent = formatPrice(total);
    }
}

// دالة لتنسيق السعر (مطابقة لتنسيق PHP)
function formatPrice(price) {
    // استخدام نفس التنسيق المستخدم في PHP: number_format($price, 2) . ' د.م.'
    const formatted = parseFloat(price).toFixed(2);
    // إضافة فواصل للأرقام الكبيرة
    return formatted.replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' د.م.';
}

// إضافة مستمعات الأحداث لجميع حقول الكمية
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
        // تحديث عند تغيير القيمة (يحدث SESSION)
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const price = parseFloat(this.getAttribute('data-price'));
            updateItemTotal(productId, price);
        });
        
        // تحديث الواجهة فقط عند الكتابة (بدون تحديث SESSION)
        input.addEventListener('input', function() {
            const productId = this.getAttribute('data-product-id');
            const price = parseFloat(this.getAttribute('data-price'));
            const quantity = parseInt(this.value) || 1;
            const itemTotal = price * quantity;
            
            const itemTotalSpan = document.querySelector(`.item-total[data-product-id="${productId}"]`);
            if (itemTotalSpan) {
                itemTotalSpan.textContent = formatPrice(itemTotal);
            }
            updateCartTotal();
        });
        
        // التأكد من أن القيمة لا تقل عن 1 وتحديث SESSION
        input.addEventListener('blur', function() {
            if (this.value < 1) {
                this.value = 1;
            }
            const productId = this.getAttribute('data-product-id');
            const price = parseFloat(this.getAttribute('data-price'));
            updateItemTotal(productId, price);
        });
    });
    
    // تحديث الإجمالي عند تحميل الصفحة
    updateCartTotal();
    
    // تحديث SESSION عند النقر على "إتمام الطلب"
    const checkoutBtn = document.querySelector('a[href="checkout.php"]');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // تحديث جميع الكميات في SESSION قبل الانتقال
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const updates = [];
            
            quantityInputs.forEach(input => {
                const productId = input.getAttribute('data-product-id');
                const quantity = parseInt(input.value) || 1;
                updates.push({
                    product_id: productId,
                    quantity: quantity
                });
            });
            
            // تحديث جميع الكميات
            Promise.all(updates.map(update => 
                fetch('api/update_cart_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(update)
                }).then(response => response.json())
            )).then(() => {
                // بعد تحديث جميع الكميات، انتقل إلى checkout
                window.location.href = 'checkout.php';
            }).catch(error => {
                console.error('Error updating cart:', error);
                // في حالة الخطأ، انتقل على أي حال
                window.location.href = 'checkout.php';
            });
        });
    }
});

