<template>
    <article class="flex h-full flex-col transition-colors border-b border-gray-200 border-r hover:border-[#D4AF5A]">
        <div class="relative aspect-square">
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

            <button type="button"
                class="absolute right-0 top-0 flex h-8 w-8 items-center justify-center border border-gray-300 border-r-0 border-t-0 bg-white text-gray-400 transition-colors hover:bg-[#F5F0E6] hover:text-[#1E1E1E]"
                :class="{ 'bg-[#F5F0E6] text-[#1E1E1E]': inWishlist }"
                :title="inWishlist ? 'Прибрати з обраного' : 'Додати в обране'" @click="handleWishlist">
                <AppIcon name="heart" :size="16" />
            </button>
        </div>

        <div class="flex flex-1 flex-col gap-1 p-2 sm:gap-1.5 sm:p-3">
            <h3
                class="line-clamp-2 min-h-[2.2em] text-xs font-semibold leading-snug text-[#1E1E1E] sm:text-[0.8125rem]">
                <a :href="productUrl" class="transition-colors hover:text-[#D4AF5A]">{{ product.name }}</a>
            </h3>

            <div class="flex items-center gap-1.5 text-[0.6875rem] text-gray-600 sm:text-xs">
                <span class="h-1.5 w-1.5 shrink-0" :class="inStock ? 'bg-[#1E1E1E]' : 'bg-gray-400'"></span>
                <span>{{ inStock ? 'В наявності' : 'Немає в наявності' }}</span>
            </div>

            <div class="mt-0.5 flex flex-wrap items-baseline gap-1">
                <span class="font-heading text-sm font-bold text-[#1E1E1E] sm:text-[1.0625rem]">
                    {{ formatPrice(finalPrice(product)) }} грн
                </span>
                <span class="text-[0.6875rem] text-gray-600 sm:text-[0.8125rem]">/ {{ unitLabel }}</span>
            </div>

            <p v-if="product.discount > 0" class="text-[0.6875rem] text-gray-400 line-through sm:text-xs">
                {{ formatPrice(product.price) }} грн
            </p>
            <p class="text-[0.625rem] text-gray-400 sm:text-[0.6875rem]">Замовлення від 1 {{ unitLabel }}</p>

            <div class="flex flex-col gap-1 justify-end h-full w-full">
                <div v-if="inStock" class="mt-1 flex border border-gray-300">
                    <button type="button"
                        class="w-7 shrink-0 border-r border-gray-300 bg-white text-[#1E1E1E] disabled:cursor-not-allowed disabled:text-gray-300 sm:w-8"
                        :disabled="quantity <= 1" @click="decreaseQty">
                        <AppIcon name="minus" :size="14" class="mx-auto" />
                    </button>
                    <span class="flex-1 truncate px-1 py-1.5 text-center text-[0.6875rem] text-[#1E1E1E] sm:text-xs">
                        {{ quantity }} {{ unitLabel }}
                    </span>
                    <button type="button" class="w-7 shrink-0 border-l border-gray-300 bg-white text-[#1E1E1E] sm:w-8"
                        @click="increaseQty">
                        <AppIcon name="plus" :size="14" class="mx-auto" />
                    </button>
                </div>

                <button type="button"
                    class="mt-1 flex w-full items-center justify-center gap-1 whitespace-nowrap bg-[#D4AF5A] px-2 py-2 font-heading text-[0.6875rem] font-semibold text-[#1E1E1E] transition-colors hover:bg-[#C5A059] disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 sm:gap-2 sm:px-3 sm:text-[0.8125rem]"
                    :disabled="!inStock" @click="handleAddToCart">
                    <AppIcon name="shopping-cart" :size="16" class="shrink-0" />
                    <span v-if="inStock" class="hidden sm:inline">Додати до кошика</span>
                    <span v-if="inStock" class="sm:hidden">В кошик</span>
                    <span v-else>Немає в наявності</span>
                </button>

                <p v-if="product.units_per_box"
                    class="mt-1 flex items-center gap-1.5 border-t border-gray-200 pt-1.5 text-[0.625rem] text-gray-500 sm:text-[0.6875rem]">
                    <AppIcon name="info-circle" :size="14" class="shrink-0 text-gray-300" />
                    У ящику: {{ product.units_per_box }} {{ pluralUnitLabel }}
                </p>
            </div>
        </div>
    </article>
</template>

<script>
import AppIcon from './AppIcon.vue';
import { addProductToCart, toggleWishlistItem, isProductInWishlist, finalPrice, formatPrice, isInStock } from '../utils/cart.js';

export default {
    name: 'ProductCardItem',
    components: { AppIcon },
    props: {
        product: { type: Object, required: true },
        showNewBadge: { type: Boolean, default: false },
    },
    data() {
        return {
            quantity: 1,
            inWishlist: false,
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
        unitLabel() {
            return this.product.unit_name || 'шт';
        },
        pluralUnitLabel() {
            return this.product.unit_name_plural || this.product.unit_name || 'шт';
        },
    },
    mounted() {
        this.syncWishlist();
        window.addEventListener('wishlist-updated', this.syncWishlist);
    },
    unmounted() {
        window.removeEventListener('wishlist-updated', this.syncWishlist);
    },
    methods: {
        finalPrice,
        formatPrice,
        syncWishlist() {
            this.inWishlist = isProductInWishlist(this.product.id);
        },
        decreaseQty() {
            if (this.quantity > 1) this.quantity--;
        },
        increaseQty() {
            this.quantity++;
        },
        handleAddToCart() {
            if (!this.inStock) return;
            addProductToCart(this.product, this.quantity);
            this.quantity = 1;
        },
        handleWishlist() {
            toggleWishlistItem(this.product);
            this.syncWishlist();
        },
    },
};
</script>
