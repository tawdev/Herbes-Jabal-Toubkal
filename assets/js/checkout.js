// تحديد الموقع تلقائياً
document.addEventListener('DOMContentLoaded', function() {
    const getLocationBtn = document.getElementById('getLocationBtn');
    const addressTextarea = document.getElementById('user_address');
    const locationStatus = document.getElementById('locationStatus');
    const locationIcon = document.getElementById('locationIcon');
    const locationText = document.getElementById('locationText');
    
    if (getLocationBtn && addressTextarea) {
        getLocationBtn.addEventListener('click', function() {
            // تغيير حالة الزر
            getLocationBtn.disabled = true;
            getLocationBtn.style.opacity = '0.7';
            getLocationBtn.innerHTML = '<span>⏳</span><span>جاري التحديد...</span>';
            if (locationIcon) locationIcon.textContent = '⏳';
            if (locationText) {
                locationText.textContent = 'جاري تحديد موقعك...';
                locationText.style.color = '#6b7280';
            }
            
            // التحقق من دعم Geolocation API
            if (!navigator.geolocation) {
                if (locationIcon) locationIcon.textContent = '❌';
                if (locationText) {
                    locationText.textContent = 'المتصفح لا يدعم تحديد الموقع';
                    locationText.style.color = '#ef4444';
                }
                getLocationBtn.disabled = false;
                getLocationBtn.style.opacity = '1';
                getLocationBtn.innerHTML = '<span>📍</span><span>تحديد الموقع تلقائياً</span>';
                return;
            }
            
            // الحصول على الموقع
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    if (locationIcon) locationIcon.textContent = '⏳';
                    if (locationText) {
                        locationText.textContent = 'تم تحديد موقعك، جاري الحصول على العنوان...';
                        locationText.style.color = '#6b7280';
                    }
                    
                    // استخدام API محلي للحصول على العنوان (لتجنب مشكلة CORS)
                    fetch('api/get_address.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            lat: lat,
                            lon: lon
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.address) {
                                // نجح الحصول على العنوان
                                addressTextarea.value = data.address;
                                
                                if (locationIcon) locationIcon.textContent = '✓';
                                if (locationText) {
                                    locationText.textContent = 'تم تحديد موقعك بنجاح';
                                    locationText.style.color = '#10b981';
                                }
                                
                                addressTextarea.style.borderColor = '#10b981';
                                setTimeout(() => {
                                    addressTextarea.style.borderColor = '#e5e7eb';
                                }, 3000);
                            } else {
                                // فشل الحصول على العنوان، استخدم البديل
                                const fallbackAddress = data.fallback || `خط العرض: ${lat.toFixed(6)}, خط الطول: ${lon.toFixed(6)}`;
                                addressTextarea.value = fallbackAddress;
                                
                                if (locationIcon) locationIcon.textContent = '⚠️';
                                if (locationText) {
                                    locationText.textContent = 'تم تحديد الموقع (الإحداثيات فقط)';
                                    locationText.style.color = '#f59e0b';
                                }
                                
                                addressTextarea.style.borderColor = '#f59e0b';
                                setTimeout(() => {
                                    addressTextarea.style.borderColor = '#e5e7eb';
                                }, 3000);
                            }
                            
                            getLocationBtn.disabled = false;
                            getLocationBtn.style.opacity = '1';
                            getLocationBtn.innerHTML = '<span>📍</span><span>تحديد الموقع تلقائياً</span>';
                        })
                        .catch(error => {
                            console.error('Error fetching address:', error);
                            
                            // في حالة الخطأ، استخدم الإحداثيات كبديل
                            const fallbackAddress = `خط العرض: ${lat.toFixed(6)}, خط الطول: ${lon.toFixed(6)}`;
                            addressTextarea.value = fallbackAddress;
                            
                            if (locationIcon) locationIcon.textContent = '⚠️';
                            if (locationText) {
                                let errorMsg = 'تم تحديد الموقع (الإحداثيات فقط)';
                                
                                if (error.message && error.message.includes('HTTP')) {
                                    errorMsg += ' - مشكلة في الاتصال بالخادم';
                                } else if (error.message && error.message.includes('Failed to fetch')) {
                                    errorMsg += ' - مشكلة في الاتصال بالإنترنت';
                                } else {
                                    errorMsg += ' - لم يتم الحصول على العنوان';
                                }
                                
                                locationText.textContent = errorMsg;
                                locationText.style.color = '#f59e0b';
                            }
                            
                            addressTextarea.style.borderColor = '#f59e0b';
                            setTimeout(() => {
                                addressTextarea.style.borderColor = '#e5e7eb';
                            }, 3000);
                            
                            getLocationBtn.disabled = false;
                            getLocationBtn.style.opacity = '1';
                            getLocationBtn.innerHTML = '<span>📍</span><span>تحديد الموقع تلقائياً</span>';
                        });
                },
                function(error) {
                    let errorMessage = 'حدث خطأ أثناء تحديد الموقع';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'تم رفض طلب الوصول إلى الموقع. يرجى السماح بالوصول إلى الموقع في إعدادات المتصفح.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'معلومات الموقع غير متاحة.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'انتهت مهلة طلب الموقع.';
                            break;
                    }
                    
                    if (locationIcon) locationIcon.textContent = '❌';
                    if (locationText) {
                        locationText.textContent = errorMessage;
                        locationText.style.color = '#ef4444';
                    }
                    getLocationBtn.disabled = false;
                    getLocationBtn.style.opacity = '1';
                    getLocationBtn.innerHTML = '<span>📍</span><span>تحديد الموقع تلقائياً</span>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
        
        // إضافة تأثير hover للزر
        getLocationBtn.addEventListener('mouseenter', function() {
            if (!getLocationBtn.disabled) {
                getLocationBtn.style.transform = 'translateY(-2px)';
                getLocationBtn.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            }
        });
        
        getLocationBtn.addEventListener('mouseleave', function() {
            getLocationBtn.style.transform = 'translateY(0)';
            getLocationBtn.style.boxShadow = 'none';
        });
    }
});

