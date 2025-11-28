// فلترة وترتيب المنتجات
function filterProducts() {
    const category = document.getElementById('categoryFilter').value;
    const sort = document.getElementById('sortFilter').value;
    
    // إعادة تحميل الصفحة مع المعاملات الجديدة
    const url = new URL(window.location.href);
    url.searchParams.set('category', category);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
}

