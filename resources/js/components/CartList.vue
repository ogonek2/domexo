<template>
    <div class="checkout-cart">
        <div v-if="cart.length > 0" class="checkout-cart__list">
            <div v-for="item in cart" :key="item.id" class="checkout-cart__item">
                <div class="checkout-cart__thumb">
                    <img v-if="item.image" :src="item.image" :alt="item.name">
                    <AppIcon v-else name="image" :size="22" icon-class="text-gray-300" />
                </div>

                <div class="checkout-cart__info">
                    <h4 class="checkout-cart__name">{{ item.name }}</h4>
                    <p v-if="item.articule" class="checkout-cart__articule">Артикул: {{ item.articule }}</p>
                    <div class="checkout-cart__price-row">
                        <span class="checkout-cart__price">{{ formatPrice(getItemPrice(item)) }} ₴</span>
                        <span v-if="isWholesaleActive(item)" class="checkout-cart__opt">Опт</span>
                    </div>

                    <div class="checkout-cart__actions">
                        <div class="pcard__qty checkout-cart__qty">
                            <button type="button" class="pcard__qty-btn" :disabled="item.quantity <= 1" @click="decrease(item.id)">
                                <AppIcon name="minus" :size="12" />
                            </button>
                            <span class="pcard__qty-value">{{ item.quantity }}</span>
                            <button type="button" class="pcard__qty-btn" @click="increase(item.id)">
                                <AppIcon name="plus" :size="12" />
                            </button>
                        </div>
                        <button type="button" class="checkout-cart__remove" @click="remove(item.id)" title="Видалити">
                            <AppIcon name="trash" :size="14" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="checkout-cart__empty">
            <AppIcon name="shopping-cart" :size="36" icon-class="text-gray-300" />
            <h4>Кошик порожній</h4>
            <p>Додайте товари для оформлення замовлення</p>
            <a href="/catalog" class="btn-domiko-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm">
                <AppIcon name="shopping-bag" :size="16" />
                До каталогу
            </a>
        </div>

        <div v-if="cart.length > 0" class="checkout-cart__total">
            <div class="checkout-cart__total-row">
                <span>Товарів</span>
                <span>{{ totalItems }} шт</span>
            </div>
            <div class="checkout-cart__total-row checkout-cart__total-row--sum">
                <span>Разом</span>
                <span>{{ formatPrice(totalPrice) }} ₴</span>
            </div>
            <p v-if="isBelowMinimum" class="checkout-cart__notice checkout-cart__notice--warn">
                Мінімальна сума — 1000 ₴. Додайте ще на {{ formatPrice(amountToReachMinimum) }} ₴.
            </p>
            <p v-else class="checkout-cart__notice checkout-cart__notice--ok">
                Мінімальну суму досягнуто — можна оформлювати.
            </p>
        </div>

        <input type="hidden" id="total_price_stream" :value="totalPrice">
    </div>
</template>

<script>
import AppIcon from './AppIcon.vue';

const MIN_ORDER_TOTAL = 1000;

export default {
    name: 'CartList',
    components: { AppIcon },
    data() {
        return { cart: [] };
    },
    computed: {
        totalPrice() {
            const total = this.cart.reduce((sum, item) => sum + this.getItemPrice(item) * item.quantity, 0);
            return Number(total.toFixed(2));
        },
        totalItems() {
            return this.cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        },
        isBelowMinimum() {
            return this.totalPrice < MIN_ORDER_TOTAL;
        },
        amountToReachMinimum() {
            const difference = MIN_ORDER_TOTAL - this.totalPrice;
            return difference > 0 ? Math.ceil(difference) : 0;
        },
    },
    mounted() {
        this.loadCart();
        window.addEventListener('cart-updated', this.loadCart);
    },
    unmounted() {
        window.removeEventListener('cart-updated', this.loadCart);
    },
    methods: {
        loadCart() {
            try {
                this.cart = JSON.parse(localStorage.getItem('cart') || '[]');
            } catch {
                this.cart = [];
            }
        },
        saveCart() {
            localStorage.setItem('cart', JSON.stringify(this.cart));
            window.dispatchEvent(new Event('cart-updated'));
        },
        increase(id) {
            const item = this.cart.find((entry) => entry.id == id);
            if (item) {
                item.quantity++;
                this.saveCart();
            }
        },
        decrease(id) {
            const item = this.cart.find((entry) => entry.id == id);
            if (item && item.quantity > 1) {
                item.quantity--;
                this.saveCart();
            }
        },
        remove(id) {
            this.cart = this.cart.filter((entry) => entry.id != id);
            this.saveCart();
        },
        getItemPrice(item) {
            if (item.isWholesale && item.wholesalePrice && item.wholesaleMinQuantity && item.quantity >= item.wholesaleMinQuantity) {
                return parseFloat(item.wholesalePrice);
            }

            let price = item.price;
            if (typeof price === 'string') {
                price = parseFloat(price.replace(/[^\d.,]/g, '').replace(',', '.'));
            }

            return isNaN(price) ? 0 : price;
        },
        isWholesaleActive(item) {
            return item.isWholesale && item.wholesalePrice && item.wholesaleMinQuantity && item.quantity >= item.wholesaleMinQuantity;
        },
        formatPrice(price) {
            return Math.round(price).toLocaleString('uk-UA');
        },
    },
};
</script>

<style scoped>
.checkout-cart__list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 24rem;
    overflow-y: auto;
    padding-right: 0.25rem;
}

.checkout-cart__item {
    display: grid;
    grid-template-columns: 64px 1fr;
    gap: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #eee;
    background: #fff;
}

.checkout-cart__thumb {
    width: 64px;
    height: 64px;
    border: 1px solid #eee;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkout-cart__thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.checkout-cart__name {
    margin: 0 0 0.2rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #1E1E1E;
    line-height: 1.35;
}

.checkout-cart__articule {
    margin: 0 0 0.35rem;
    font-size: 0.6875rem;
    color: #888;
}

.checkout-cart__price {
    font-weight: 700;
    font-size: 0.875rem;
    font-family: Montserrat, sans-serif;
}

.checkout-cart__opt {
    margin-left: 0.4rem;
    font-size: 0.625rem;
    background: #D4AF5A;
    color: #1E1E1E;
    padding: 0.1rem 0.35rem;
}

.checkout-cart__actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.checkout-cart__qty { max-width: 140px; }

.checkout-cart__remove {
    background: none;
    border: 1px solid #ddd;
    padding: 0.3rem 0.4rem;
    color: #666;
    cursor: pointer;
}

.checkout-cart__remove:hover { border-color: #1E1E1E; color: #1E1E1E; }

.checkout-cart__empty {
    text-align: center;
    padding: 2rem 1rem;
    border: 1px solid #eee;
    background: #F5F6FB;
}

.checkout-cart__empty h4 {
    font-family: Montserrat, sans-serif;
    font-size: 1rem;
    margin: 0.75rem 0 0.35rem;
    color: #0B1F3B;
}

.checkout-cart__empty p {
    color: #666;
    font-size: 0.8125rem;
    margin: 0 0 1rem;
}

.checkout-cart__total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.checkout-cart__total-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.8125rem;
    color: #666;
    margin-bottom: 0.4rem;
}

.checkout-cart__total-row--sum {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1E1E1E;
    font-family: Montserrat, sans-serif;
    margin: 0.75rem 0 1rem;
}

.checkout-cart__notice {
    font-size: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #eee;
    margin: 0;
}

.checkout-cart__notice--warn { background: #fff8f0; border-color: #D4AF5A; color: #666; }
.checkout-cart__notice--ok { background: #F5F0E6; color: #1E1E1E; }
</style>
