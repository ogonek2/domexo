<template>
    <div class="w-full">
        <div v-if="!loading && items.length > 0"
             data-product-grid
             class="grid grid-cols-2 sm:grid-cols-3 g:grid-cols-4 xl:grid-cols-5">
            <ProductCardItem
                v-for="product in items"
                :key="product.id"
                :product="product"
                :show-new-badge="showNewBadge"
            />
        </div>

        <div v-if="loading" class="py-12 text-center text-gray-500">
            <div class="mx-auto mb-4 h-8 w-8 animate-spin border-2 border-gray-200 border-t-[#D4AF5A]"></div>
            <p class="text-sm">Завантаження товарів...</p>
        </div>

        <div v-if="!loading && items.length === 0" class="px-4 py-12 text-center text-gray-500">
            <AppIcon name="box" :size="40" icon-class="mx-auto mb-4 text-gray-300" />
            <h5 class="mb-2 font-bold text-[#1E1E1E]">Товари не знайдено</h5>
            <p class="text-sm">Спробуйте змінити параметри пошуку</p>
        </div>
    </div>
</template>

<script>
import ProductCardItem from './ProductCardItem.vue';
import AppIcon from './AppIcon.vue';

export default {
    name: 'ProductList',
    components: { ProductCardItem, AppIcon },
    props: {
        products: { type: Array, default: () => [] },
        pagination: { type: Object, default: () => ({}) },
        showNewBadge: { type: Boolean, default: false },
    },
    data() {
        return {
            loading: false,
            items: [],
            page: {},
        };
    },
    mounted() {
        this.hydrateFromPropsOrDataset();
    },
    watch: {
        products: { deep: true, handler() { this.hydrateFromPropsOrDataset(); } },
        pagination: { deep: true, handler() { this.hydrateFromPropsOrDataset(); } },
    },
    methods: {
        hydrateFromPropsOrDataset() {
            const fromProps = Array.isArray(this.products) ? this.products : [];
            if (fromProps.length > 0) {
                this.items = fromProps;
                this.page = this.pagination && typeof this.pagination === 'object' ? this.pagination : {};
                return;
            }

            const host = this.$el?.closest?.('[data-products]') || this.$el;
            const rawProducts = host?.getAttribute?.('data-products') ?? host?.dataset?.products ?? '';
            const rawPagination = host?.getAttribute?.('data-pagination') ?? host?.dataset?.pagination ?? '';

            try {
                const parsed = rawProducts ? JSON.parse(rawProducts) : [];
                this.items = Array.isArray(parsed) ? parsed : [];
            } catch {
                this.items = [];
            }

            try {
                const parsed = rawPagination ? JSON.parse(rawPagination) : {};
                this.page = parsed && typeof parsed === 'object' ? parsed : {};
            } catch {
                this.page = {};
            }
        },
    },
};
</script>
