<template>
    <div>
        <section class="relative flex min-h-[520px] items-center overflow-hidden md:min-h-[580px]">
            <div class="absolute inset-0 bg-[#0B1F3B]">
                <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?w=1920&q=80"
                     alt=""
                     class="h-full w-full object-cover opacity-40"
                     loading="eager">
                <div class="hero-overlay absolute inset-0"></div>
            </div>

            <div class="relative mx-auto w-full max-w-site px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <div class="max-w-2xl">
                    <p class="mb-4 font-heading text-xs font-medium uppercase tracking-brand-wide text-[#D4AF5A] sm:text-sm">
                        Home &bull; Kitchen &bull; Bath
                    </p>
                    <h1 class="mb-4 font-heading text-3xl font-bold leading-tight text-white sm:text-4xl md:text-5xl lg:text-[3.25rem]">
                        Товари для дому,<br>кухні та ванної
                    </h1>
                    <p class="mb-8 font-heading text-sm font-medium uppercase tracking-brand text-white/80 sm:text-base">
                        Оптом і в роздріб
                    </p>
                    <a href="/catalog"
                       class="btn-domiko-primary inline-flex items-center gap-3 px-8 py-3.5 text-base shadow-lg">
                        Перейти в каталог
                        <AppIcon name="arrow-right" :size="20" />
                    </a>
                </div>
            </div>
        </section>

        <section class="border-b border-gray-100 bg-white shadow-sm">
            <div class="mx-auto max-w-site px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 divide-y divide-gray-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4">
                    <div v-for="feature in features" :key="feature.label" class="flex items-center gap-4 px-4 py-5 lg:px-6">
                        <AppIcon :name="feature.icon" :size="24" />
                        <span class="text-sm font-medium text-[#1E1E1E]">{{ feature.label }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-12 md:py-16">
            <div class="mx-auto max-w-site px-4 sm:px-6 lg:px-8">
                <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] md:text-3xl">Усі категорії</h2>
                        <p class="mt-1.5 text-sm text-slate-500">Оберіть розділ і одразу переходьте до товарів</p>
                    </div>
                    <a href="/catalog"
                       class="inline-flex items-center gap-2 bg-[#0B1F3B] px-5 py-2.5 font-heading text-sm font-semibold text-white transition-colors hover:bg-[#132a4d]">
                        Весь каталог
                        <AppIcon name="arrow-right" :size="16" />
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-4">
                    <CategoryCard
                        v-for="(category, index) in categories"
                        :key="category.url"
                        :category="category"
                        :index="index" />
                </div>
            </div>
        </section>

        <ProductRibbon
            :new-products="newProducts"
            :sale-products="saleProducts"
            :wholesale-products="wholesaleProducts"
            :popular-products="popularProducts"
            :catalog-url="catalogUrl" />
    </div>
</template>

<script>
import { computed } from 'vue';
import { spaState } from '../spa/spaStore.js';
import AppIcon from '../components/AppIcon.vue';
import CategoryCard from '../components/CategoryCard.vue';
import ProductRibbon from '../components/ProductRibbon.vue';

export default {
    name: 'HomePage',
    components: { AppIcon, CategoryCard, ProductRibbon },
    setup() {
        const data = computed(() => spaState.pageData || {});

        return {
            popularProducts: computed(() => data.value.popularProducts || []),
            newProducts: computed(() => data.value.newProducts || []),
            saleProducts: computed(() => data.value.saleProducts || []),
            wholesaleProducts: computed(() => data.value.wholesaleProducts || []),
            categories: computed(() => data.value.categories || []),
            catalogUrl: computed(() => data.value.routes?.catalog || '/catalog'),
            features: [
                { icon: 'package', label: 'Широкий асортимент' },
                { icon: 'award', label: 'Якість і надійність' },
                { icon: 'handshake', label: 'Вигідні умови' },
                { icon: 'truck', label: 'Швидка доставка' },
            ],
        };
    },
};
</script>
