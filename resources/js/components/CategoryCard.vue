<template>
    <a :href="category.href" class="category-tile group" :style="{ '--tile-bg': tileBg }">
        <div class="category-tile__content">
            <div class="category-tile__text">
                <h3 class="category-tile__title">{{ category.name }}</h3>
                <p class="category-tile__desc">{{ subtitle }}</p>
            </div>
            <span class="category-tile__arrow" aria-hidden="true">
                <AppIcon name="arrow-right" :size="16" />
            </span>
        </div>
        <div class="category-tile__media">
            <img
                v-if="category.image"
                :src="category.image"
                :alt="category.name"
                loading="lazy"
                class="category-tile__image" />
            <div v-else class="category-tile__placeholder">
                <AppIcon name="layers" :size="36" />
            </div>
        </div>
    </a>
</template>

<script>
import AppIcon from './AppIcon.vue';

const TILE_COLORS = [
    '#EEF1F6',
    '#F5F0E6',
    '#E8EEF5',
    '#F3EEE8',
    '#E9F0EC',
    '#F0EBEF',
    '#ECEFF4',
    '#F6F1EA',
];

export default {
    name: 'CategoryCard',
    components: { AppIcon },
    props: {
        category: { type: Object, required: true },
        index: { type: Number, default: 0 },
    },
    computed: {
        tileBg() {
            return TILE_COLORS[this.index % TILE_COLORS.length];
        },
        subtitle() {
            if (this.category.description) {
                return this.category.description;
            }
            const count = this.category.count ?? 0;
            return count === 1 ? '1 товар' : `${count} товарів`;
        },
    },
};
</script>
