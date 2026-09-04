<template>
    <article
        class="pcard flex h-full flex-col transition-colors border-b border-gray-200 border-r hover:border-[#D4AF5A]"
        :class="{ 'pcard--oos': !inStock, 'pcard--in-cart': inCart }">
        <div class="relative aspect-square" :class="{ 'opacity-55 grayscale': !inStock }">
            <a :href="productUrl" class="block h-full w-full" :title="product.name">
                <img v-if="product.image_path" :src="product.image_path" :alt="product.name" loading="lazy"
                    class="h-full w-full object-contain p-1" />
                <div v-else class="flex h-full w-full items-center justify-center text-gray-300">
                    <AppIcon name="image" :size="28" />
                </div>
            </a>

            <span v-if="product.discount > 0"
                class="absolute left-0 top-0 bg-[#1E1E1E] px-2 py-1 text-[0.6875rem] font-bold uppercase tracking-wide text-white">
                -{{ product.discount }}%
            </span>
            <span v-else-if="showNewBadge"
                class="absolute left-0 top-0 bg-[#D4AF5A] px-2 py-1 text-[0.6875rem] font-bold uppercase tracking-wide text-[#1E1E1E]">
                Новинка
            </span>

            <span
                v-if="inCart"
                class="absolute bottom-0 left-0 bg-[#0B1F3B] px-2 py-1 text-[0.625rem] font-semibold uppercase tracking-wide text-white">
                У кошику · {{ cartQuantity }}
            </span>

            <button type="button"
                class="absolute right-0 top-0 flex h-8 w-8 items-center justify-center border border-gray-300 border-r-0 border-t-0 bg-white text-gray-400 transition-colors hover:bg-[#F5F0E6] hover:text-[#1E1E1E]"
                :class="{ 'bg-[#F5F0E6] text-[#1E1E1E]': inWishlist }"
                :title="inWishlist ? 'Прибрати з обраного' : 'Додати в обране'" @click="handleWishlist">
                <AppIcon name="heart" :size="16" />
            </button>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-2 sm:gap-2.5 sm:p-3" :class="{ 'opacity-70': !inStock }">
            <h3 class="pcard__title line-clamp-2 text-xs font-semibold leading-snug text-[#1E1E1E] sm:text-[0.8125rem]">
                <a :href="productUrl" class="transition-colors hover:text-[#D4AF5A]">{{ product.name }}</a>
            </h3>

            <a
                v-if="inStock"
                :href="productUrl"
                class="pcard__stock pcard__stock--in inline-flex items-center gap-1.5 text-[0.6875rem] font-medium text-[#16A34A] transition-colors hover:text-[#15803D] sm:text-xs">
                <span class="h-1.5 w-1.5 shrink-0 bg-[#16A34A]"></span>
                В наявності
            </a>
            <span
                v-else
                class="pcard__stock inline-flex items-center gap-1.5 text-[0.6875rem] font-medium text-gray-400 sm:text-xs">
                <span class="h-1.5 w-1.5 shrink-0 bg-gray-400"></span>
                Немає в наявності
            </span>

            <div class="mt-0.5 flex flex-wrap items-baseline gap-1">
                <span class="font-heading text-sm font-bold text-[#1E1E1E] sm:text-[1.0625rem]">
                    {{ formatPrice(finalPrice(product)) }} грн
                </span>
                <span class="text-[0.6875rem] text-gray-600 sm:text-[0.8125rem]">/ {{ unitLabel }}</span>
            </div>

            <p v-if="product.discount > 0" class="text-[0.6875rem] text-gray-400 line-through sm:text-xs">
                {{ formatPrice(product.price) }} грн
            </p>

            <button
                v-if="hasWholesale"
                type="button"
                class="pcard__wholesale"
                :class="{ 'pcard__wholesale--active': quantity >= wholesaleMinQty }"
                :title="`Обрати опт: ${wholesaleMinQty} ${pluralUnitLabel}`"
                @click="selectWholesaleQty">
                <AppIcon name="package" :size="14" class="shrink-0" />
                <span class="pcard__wholesale-text">
                    <span class="pcard__wholesale-price">{{ formatPrice(product.wholesale_price) }} грн</span>
                    <span class="pcard__wholesale-from">опт від {{ wholesaleMinQty }} {{ pluralUnitLabel }}</span>
                </span>
            </button>

            <p v-else class="text-[0.625rem] text-gray-400 sm:text-[0.6875rem]">Замовлення від 1 {{ unitLabel }}</p>

            <div class="flex flex-col gap-1 justify-end h-full w-full">
                <div v-if="inStock" class="pcard__qty mt-1">
                    <button type="button"
                        class="pcard__qty-btn"
                        :disabled="quantity <= 1"
                        aria-label="Зменшити кількість"
                        @click="decreaseQty">
                        <AppIcon name="minus" :size="16" class="mx-auto" />
                    </button>
                    <label class="pcard__qty-field">
                        <input
                            ref="qtyInput"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            autocomplete="off"
                            class="pcard__qty-input"
                            aria-label="Кількість"
                            :value="quantityInput"
                            @keydown="onQtyKeydown"
                            @paste="onQtyPaste"
                            @input="onQtyInput"
                            @keydown.enter.prevent="handleAddToCart"
                            @blur="normalizeQty" />
                        <span class="pcard__qty-unit">{{ unitLabel }}</span>
                    </label>
                    <button type="button"
                        class="pcard__qty-btn"
                        aria-label="Збільшити кількість"
                        @click="increaseQty">
                        <AppIcon name="plus" :size="16" class="mx-auto" />
                    </button>
                </div>

                <button type="button"
                    class="pcard__cart-btn mt-1 flex w-full items-center justify-center gap-1 whitespace-nowrap px-2 py-2 font-heading text-[0.6875rem] font-semibold transition-colors disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 sm:gap-2 sm:px-3 sm:text-[0.8125rem]"
                    :class="inCart
                        ? 'bg-[#0B1F3B] text-white hover:bg-[#16375f]'
                        : 'bg-[#D4AF5A] text-[#1E1E1E] hover:bg-[#C5A059]'"
                    :disabled="!inStock"
                    @click="handleAddToCart">
                    <AppIcon name="shopping-cart" :size="16" class="shrink-0" />
                    <span v-if="!inStock">Немає в наявності</span>
                    <template v-else-if="inCart">
                        <span class="hidden sm:inline">У кошику · ще +{{ quantity }}</span>
                        <span class="sm:hidden">У кошику +{{ quantity }}</span>
                    </template>
                    <template v-else>
                        <span class="hidden sm:inline">Додати до кошика</span>
                        <span class="sm:hidden">В кошик</span>
                    </template>
                </button>

                <a
                    v-if="inCart"
                    href="/koshyk"
                    class="mt-0.5 text-center text-[0.6875rem] font-semibold text-[#0B1F3B] transition-colors hover:text-[#D4AF5A]">
                    Перейти в кошик
                </a>

                <button
                    v-if="boxQty"
                    type="button"
                    class="pcard__box-btn mt-1 flex w-full items-center gap-1.5 border-t border-gray-200 pt-1.5 text-left text-[0.625rem] text-gray-500 transition-colors hover:text-[#0B1F3B] sm:text-[0.6875rem]"
                    :title="`Обрати ${boxQty} ${pluralUnitLabel}`"
                    @click="selectBoxQty">
                    <AppIcon name="boxes" :size="14" class="shrink-0 text-gray-400" />
                    У ящику: {{ boxQty }} {{ pluralUnitLabel }}
                </button>
            </div>
        </div>
    </article>
</template>

<script>
import AppIcon from './AppIcon.vue';
import {
    addProductToCart,
    toggleWishlistItem,
    isProductInWishlist,
    finalPrice,
    formatPrice,
    isInStock,
    hasWholesaleOffer,
    getCartQuantity,
    parseQuantity,
    onQtyKeydown,
    onQtyPaste,
    filterQtyInputEvent,
} from '../utils/cart.js';

export default {
    name: 'ProductCardItem',
    components: { AppIcon },
    props: {
        product: { type: Object, required: true },
        showNewBadge: { type: Boolean, default: false },
    },
    data() {
        return {
            quantityInput: '1',
            inWishlist: false,
            cartQuantity: 0,
        };
    },
    computed: {
        productUrl() {
            const cat = this.product.category_url || 'catalog';
            return `/catalog/categoriya/${cat}/${this.product.url}`;
        },
        inStock() {
            return isInStock(this.product);
        },
        inCart() {
            return this.cartQuantity > 0;
        },
        quantity() {
            return parseQuantity(this.quantityInput, 1);
        },
        unitLabel() {
            return this.product.unit_name || 'шт';
        },
        pluralUnitLabel() {
            return this.product.unit_name_plural || this.product.unit_name || 'шт';
        },
        hasWholesale() {
            return hasWholesaleOffer(this.product);
        },
        wholesaleMinQty() {
            return parseQuantity(this.product.wholesale_min_quantity, 0);
        },
        boxQty() {
            return parseQuantity(this.product.units_per_box, 0);
        },
    },
    mounted() {
        this.syncWishlist();
        this.syncCart();
        window.addEventListener('wishlist-updated', this.syncWishlist);
        window.addEventListener('cart-updated', this.syncCart);
    },
    unmounted() {
        window.removeEventListener('wishlist-updated', this.syncWishlist);
        window.removeEventListener('cart-updated', this.syncCart);
    },
    methods: {
        finalPrice,
        formatPrice,
        onQtyKeydown,
        onQtyPaste,
        syncWishlist() {
            this.inWishlist = isProductInWishlist(this.product.id);
        },
        syncCart() {
            this.cartQuantity = getCartQuantity(this.product.id);
        },
        readQty() {
            const fromDom = this.$refs.qtyInput?.value;
            return parseQuantity(fromDom ?? this.quantityInput, 1);
        },
        setQty(value) {
            const qty = parseQuantity(value, 1);
            this.quantityInput = String(qty);
        },
        onQtyInput(event) {
            this.quantityInput = filterQtyInputEvent(event);
        },
        decreaseQty() {
            this.setQty(Math.max(1, this.readQty() - 1));
        },
        increaseQty() {
            this.setQty(this.readQty() + 1);
        },
        normalizeQty() {
            this.setQty(this.readQty());
        },
        selectWholesaleQty() {
            if (!this.wholesaleMinQty) return;
            this.setQty(this.wholesaleMinQty);
        },
        selectBoxQty() {
            if (!this.boxQty) return;
            this.setQty(this.boxQty);
        },
        handleAddToCart() {
            if (!this.inStock) return;
            const qty = this.readQty();
            this.setQty(qty);
            addProductToCart(this.product, qty);
            this.syncCart();
        },
        handleWishlist() {
            toggleWishlistItem(this.product);
            this.syncWishlist();
        },
    },
};
</script>
