export function formatPrice(price) {
    const num = typeof price === 'string'
        ? parseFloat(price.replace(/[^\d.,]/g, '').replace(',', '.'))
        : price;
    return Math.round(num || 0).toLocaleString('uk-UA');
}

export function finalPrice(product) {
    const price = parseFloat(product.price) || 0;
    const discount = parseFloat(product.discount) || 0;
    if (discount > 0) {
        return price * (1 - discount / 100);
    }
    return price;
}

export function isInStock(product) {
    const a = product.availability;
    return a !== 2 && a !== '2' && a !== 'out_of_stock' && a !== false && a !== 0;
}

export function addProductToCart(product, quantity = 1) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const id = product.id;
    const price = finalPrice(product);
    const existing = cart.find(item => item.id == id);

    if (existing) {
        existing.quantity += quantity;
    } else {
        const item = {
            id,
            name: product.name,
            price,
            image: product.image_path || product.image || '',
            articule: product.articule || 'Не вказано',
            quantity,
        };

        if (product.is_wholesale && product.wholesale_price && product.wholesale_min_quantity) {
            item.isWholesale = true;
            item.wholesalePrice = parseFloat(product.wholesale_price);
            item.wholesaleMinQuantity = parseInt(product.wholesale_min_quantity, 10);
        }

        cart.push(item);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    window.dispatchEvent(new Event('cart-updated'));

    if (window.$toast) {
        window.$toast.success('Додано в кошик');
    }
}

export function toggleWishlistItem(product) {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    const id = product.id;
    const index = wishlist.findIndex(item => (typeof item === 'object' ? item.id : item) == id);

    if (index > -1) {
        wishlist.splice(index, 1);
        if (window.$toast) window.$toast.info('Видалено з обраного');
    } else {
        wishlist.push({
            id,
            name: product.name,
            price: finalPrice(product),
            image: product.image_path || product.image || '',
            image_path: product.image_path || product.image || '',
            url: product.url || '',
            category_url: product.category_url || 'catalog',
            articule: product.articule || '',
            discount: product.discount || 0,
        });
        if (window.$toast) window.$toast.success('Додано в обране');
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    window.dispatchEvent(new Event('wishlist-updated'));
}

export function isProductInWishlist(productId) {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    return wishlist.some(item => (typeof item === 'object' ? item.id : item) == productId);
}
