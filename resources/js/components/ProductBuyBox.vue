<template>
    <div class="buy-box">
        <div v-if="inStock" class="buy-box__qty pcard__qty">
            <button type="button" class="pcard__qty-btn" :disabled="quantity <= 1" @click="quantity > 1 && quantity--">
                <AppIcon name="minus" :size="16" />
            </button>
            <span class="pcard__qty-value">{{ quantity }} {{ unitLabel }}</span>
            <button type="button" class="pcard__qty-btn" @click="quantity++">
                <AppIcon name="plus" :size="16" />
            </button>
        </div>

        <div class="buy-box__actions">
            <button type="button" class="buy-box__cart" :disabled="!inStock" @click="handleAddToCart">
                <AppIcon name="shopping-cart" :size="18" />
                {{ inStock ? 'Додати до кошика' : 'Немає в наявності' }}
            </button>
            <button type="button"
                    class="buy-box__wishlist"
                    :class="{ 'buy-box__wishlist--active': inWishlist }"
                    @click="handleWishlist"
                    title="Обране">
                <AppIcon name="heart" :size="20" />
            </button>
        </div>

        <a v-if="inCart" href="/koshyk" class="buy-box__goto">
            Перейти в кошик
            <AppIcon name="arrow-right" :size="16" />
        </a>
    </div>
</template>

<script>
import AppIcon from './AppIcon.vue';
import { addProductToCart, toggleWishlistItem, isProductInWishlist, isInStock } from '../utils/cart.js';

export default {
    name: 'ProductBuyBox',
    components: { AppIcon },
    props: {
        productData: { type: Object, default: null },
    },
    data() {
        return {
            product: {},
            quantity: 1,
            inWishlist: false,
            inCart: false,
        };
    },
    computed: {
        inStock() {
            return isInStock(this.product);
        },
        unitLabel() {
            return this.product.unit_name || 'шт';
        },
    },
    watch: {
        productData: {
            deep: true,
            handler(value) {
                if (value && typeof value === 'object') {
                    this.product = { ...value };
                    this.syncState();
                }
            },
        },
    },
    mounted() {
        this.readProduct();
        this.syncState();
        window.addEventListener('cart-updated', this.syncCart);
        window.addEventListener('wishlist-updated', this.syncWishlist);
    },
    unmounted() {
        window.removeEventListener('cart-updated', this.syncCart);
        window.removeEventListener('wishlist-updated', this.syncWishlist);
    },
    methods: {
        readProduct() {
            if (this.productData && typeof this.productData === 'object') {
                this.product = { ...this.productData };
                return;
            }

            try {
                this.product = JSON.parse(this.$el?.dataset?.product || '{}');
            } catch {
                this.product = {};
            }
        },
        syncCart() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            this.inCart = cart.some(i => i.id == this.product.id);
        },
        syncWishlist() {
            this.inWishlist = isProductInWishlist(this.product.id);
        },
        syncState() {
            this.syncCart();
            this.syncWishlist();
        },
        handleAddToCart() {
            if (!this.inStock) return;
            addProductToCart(this.product, this.quantity);
            this.quantity = 1;
            this.syncCart();
        },
        handleWishlist() {
            toggleWishlistItem(this.product);
            this.syncWishlist();
        },
    },
};
</script>

<style scoped>
.buy-box__qty { margin-bottom: 0.75rem; max-width: 220px; }

.buy-box__actions {
    display: flex;
    gap: 0.5rem;
}

.buy-box__cart {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.25rem;
    background: #D4AF5A;
    border: none;
    color: #1E1E1E;
    font-family: Montserrat, sans-serif;
    font-weight: 700;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: background 0.2s;
}

.buy-box__cart:hover:not(:disabled) { background: #C5A059; }
.buy-box__cart:disabled { background: #eee; color: #999; cursor: not-allowed; }

.buy-box__wishlist {
    width: 3rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #ddd;
    color: #666;
    cursor: pointer;
    transition: all 0.2s;
}

.buy-box__wishlist:hover,
.buy-box__wishlist--active {
    border-color: #D4AF5A;
    color: #1E1E1E;
    background: #F5F0E6;
}

.buy-box__goto {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0B1F3B;
    text-decoration: none;
}

.buy-box__goto:hover { color: #D4AF5A; }
</style>
