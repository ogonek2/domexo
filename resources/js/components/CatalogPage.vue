<template>
    <div class="bg-[#F5F6FB]">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-site px-4 py-6 sm:px-6 lg:px-8">
                <ol class="flex flex-wrap items-center gap-1.5 text-[0.8125rem] text-slate-500">
                    <li>
                        <a
                            :href="config.routes?.home || '/'"
                            class="transition-colors hover:text-[#0B1F3B]"
                            aria-label="Головна"
                            @click="onCrumbClick($event, config.routes?.home || '/')">
                            <AppIcon name="home" :size="16" />
                        </a>
                    </li>
                    <template v-for="(crumb, index) in breadcrumbs" :key="`${crumb.label}-${index}`">
                        <li><AppIcon name="chevron-right" :size="14" class="text-gray-300" /></li>
                        <li>
                            <a
                                v-if="crumb.url && index < breadcrumbs.length - 1"
                                :href="crumb.url"
                                class="transition-colors hover:text-[#0B1F3B]"
                                @click="onCrumbClick($event, crumb.url)">
                                {{ crumb.label }}
                            </a>
                            <span
                                v-else
                                class="max-w-xs truncate font-medium text-[#1E1E1E]"
                                aria-current="page">
                                {{ crumb.label }}
                            </span>
                        </li>
                    </template>
                </ol>
            </div>
        </nav>

        <!-- Main -->
        <section class="mx-auto max-w-site px-4 py-8 sm:px-6 md:py-12 lg:px-8">
            <div class="grid items-start gap-2 lg:grid-cols-[260px_1fr] lg:gap-2">
                <aside class="catalog-filters-drawer fixed left-0 z-[72] w-[min(300px,88vw)] -translate-x-full transition-transform duration-300 ease-out lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:z-auto lg:w-auto lg:self-start lg:translate-x-0 lg:transition-none"
                       :class="{ 'translate-x-0': filtersOpen }">
                    <div class="catalog-filters-drawer__inner h-full lg:max-h-[calc(100vh-var(--header-height)-2rem)] lg:overflow-hidden lg:border lg:border-gray-200">
                        <CatalogFilters
                            v-model="filters"
                            :category-tree="categoryTree"
                            :selected-category-url="currentCategoryUrl"
                            @navigate-category="navigateToCategory"
                            @reset="resetFilters"
                            @close="closeFilters" />
                    </div>
                </aside>

                <div v-if="filtersOpen"
                     class="catalog-filters-backdrop fixed inset-x-0 bottom-0 z-[60] bg-[#0B1F3B]/45 lg:hidden"
                     :style="{ top: 'var(--header-height, 108px)' }"
                     @click="closeFilters"></div>

                <div id="catalog-products-anchor">
                    <div v-if="activeFilterTags.length" class="mb-3 flex flex-wrap items-center gap-2 px-0.5">
                        <span class="text-[0.6875rem] font-semibold uppercase tracking-wide text-slate-400">Активні</span>
                        <button
                            v-for="tag in activeFilterTags"
                            :key="tag.key"
                            type="button"
                            class="catalog-active-tag"
                            :title="`Прибрати: ${tag.label}`"
                            @click="removeFilter(tag.key)">
                            {{ tag.label }}
                            <AppIcon name="x" :size="12" />
                        </button>
                        <button
                            type="button"
                            class="text-[0.75rem] font-medium text-[#D4AF5A] transition-colors hover:text-[#C5A059]"
                            @click="resetFilters">
                            Скинути
                        </button>
                    </div>

                    <div class="mb-5 flex flex-wrap items-center justify-between gap-4 border border-gray-200 bg-white px-5 py-4">
                        <div>
                            <h1 v-if="config.hero?.title" class="font-heading text-lg font-bold text-[#0B1F3B]">
                                {{ config.hero.title }}
                            </h1>
                            <p class="text-sm text-slate-500">
                                Знайдено <strong class="font-bold text-[#0B1F3B]">{{ totalCount }}</strong>
                                {{ totalCount === 1 ? 'товар' : 'товарів' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button"
                                    class="relative inline-flex items-center gap-1.5 border border-[#0B1F3B] px-3.5 py-2 font-heading text-xs font-semibold text-[#0B1F3B] lg:hidden"
                                    @click="openFilters">
                                <AppIcon name="sliders-horizontal" :size="16" />
                                Фільтри
                                <span v-if="activeFiltersCount"
                                      class="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center bg-[#D4AF5A] px-1 text-[0.625rem] font-bold text-[#1E1E1E]">
                                    {{ activeFiltersCount }}
                                </span>
                            </button>
                            <div class="flex items-center gap-2">
                                <label for="catalogSort" class="whitespace-nowrap text-xs text-slate-500">Сортування</label>
                                <select id="catalogSort" v-model="sort" class="border border-gray-300 bg-white px-3 py-2 text-xs focus:border-[#D4AF5A] focus:outline-none">
                                    <option value="default">За замовчуванням</option>
                                    <option value="price_asc">Ціна: від низької</option>
                                    <option value="price_desc">Ціна: від високої</option>
                                    <option value="name_asc">Назва: А–Я</option>
                                    <option value="name_desc">Назва: Я–А</option>
                                    <option v-if="config.showNewest" value="newest">Новинки</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div v-if="loading" class="mb-4 flex items-center justify-center gap-3 border border-gray-200 bg-white py-6 text-sm text-slate-500">
                        <div class="h-5 w-5 animate-spin border-2 border-gray-200 border-t-[#D4AF5A]"></div>
                        Завантаження...
                    </div>

                    <div :class="{ 'opacity-50 pointer-events-none': loading }">
                        <ProductList :products="products" :show-new-badge="false" />
                    </div>

                    <nav v-if="pagination.last_page > 1" class="mt-8 border-t border-gray-200 pt-6" aria-label="Сторінки">
                        <div class="flex flex-wrap items-center justify-center gap-1.5">
                            <button v-if="pagination.current_page > 1"
                                    type="button"
                                    class="inline-flex h-9 min-w-9 items-center justify-center border border-gray-300 bg-white px-2.5 text-sm text-[#1E1E1E] transition-colors hover:border-[#0B1F3B] hover:text-[#0B1F3B]"
                                    @click="goToPage(pagination.current_page - 1)">
                                <AppIcon name="chevron-left" :size="16" />
                            </button>
                            <span v-else class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center border border-gray-200 bg-gray-50 px-2.5 text-gray-300">
                                <AppIcon name="chevron-left" :size="16" />
                            </span>

                            <template v-for="page in visiblePages" :key="page.key">
                                <span v-if="page.type === 'dots'" class="px-1 text-sm text-gray-400">…</span>
                                <span v-else-if="page.type === 'current'"
                                      class="inline-flex h-9 min-w-9 items-center justify-center border border-[#0B1F3B] bg-[#0B1F3B] px-2.5 text-sm font-bold text-white">
                                    {{ page.num }}
                                </span>
                                <button v-else
                                        type="button"
                                        class="inline-flex h-9 min-w-9 items-center justify-center border border-gray-300 bg-white px-2.5 text-sm text-[#1E1E1E] transition-colors hover:border-[#0B1F3B] hover:text-[#0B1F3B]"
                                        @click="goToPage(page.num)">
                                    {{ page.num }}
                                </button>
                            </template>

                            <button v-if="pagination.current_page < pagination.last_page"
                                    type="button"
                                    class="inline-flex h-9 min-w-9 items-center justify-center border border-gray-300 bg-white px-2.5 text-sm text-[#1E1E1E] transition-colors hover:border-[#0B1F3B] hover:text-[#0B1F3B]"
                                    @click="goToPage(pagination.current_page + 1)">
                                <AppIcon name="chevron-right" :size="16" />
                            </button>
                            <span v-else class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center border border-gray-200 bg-gray-50 px-2.5 text-gray-300">
                                <AppIcon name="chevron-right" :size="16" />
                            </span>
                        </div>
                        <p v-if="pagination.total > 0" class="mt-4 text-center text-[0.8125rem] text-slate-500">
                            Показано {{ pagination.from }}–{{ pagination.to }} з {{ pagination.total }} товарів
                        </p>
                    </nav>
                </div>
            </div>
        </section>

        <section v-if="config.popularProducts?.length" class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-site px-4 py-12 sm:px-6 md:py-16 lg:px-8">
                <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="mb-1.5 font-heading text-[0.6875rem] font-semibold uppercase tracking-[0.25em] text-[#D4AF5A]">Хіти продажів</p>
                        <h2 class="font-heading text-2xl font-bold text-[#0B1F3B]">Популярні товари</h2>
                    </div>
                    <a :href="config.routes?.catalog || '/catalog'"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0B1F3B] transition-colors hover:text-[#D4AF5A]"
                       @click="onCrumbClick($event, config.routes?.catalog || '/catalog')">
                        Всі товари <AppIcon name="arrow-right" :size="16" />
                    </a>
                </div>
                <ProductList :products="config.popularProducts" :show-new-badge="true" />
            </div>
        </section>

        <section v-if="config.showCta" class="bg-[#0B1F3B] text-white">
            <div class="mx-auto max-w-site px-4 py-12 text-center sm:px-6 md:py-16 lg:px-8">
                <h2 class="mb-3 font-heading text-2xl font-bold md:text-3xl">Не знайшли потрібний товар?</h2>
                <p class="mx-auto mb-7 max-w-lg text-white/75">Зв'яжіться з нами — допоможемо підібрати ідеальне рішення для вашого дому</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a :href="config.routes?.contacts || '#'"
                       class="inline-flex items-center gap-2 bg-[#D4AF5A] px-6 py-3.5 font-heading font-semibold text-[#1E1E1E] transition-colors hover:bg-[#C5A059]">
                        <AppIcon name="phone" :size="18" /> Зателефонувати
                    </a>
                    <a href="mailto:zmartcomua@gmail.com"
                       class="inline-flex items-center gap-2 border-2 border-white/35 px-6 py-3.5 font-heading font-semibold text-white transition-colors hover:border-white hover:bg-white/10">
                        <AppIcon name="mail" :size="18" /> Написати нам
                    </a>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import AppIcon from './AppIcon.vue';
import ProductList from './ProductList.vue';
import CatalogFilters from './CatalogFilters.vue';
import { isScrollRestoring } from '../spa/scrollMemory.js';
import { spaState } from '../spa/spaStore.js';

export default {
    name: 'CatalogPage',
    components: { AppIcon, ProductList, CatalogFilters },
    props: {
        config: { type: Object, default: () => ({}) },
    },
    data() {
        return {
            filtersOpen: false,
            loading: false,
            sort: 'default',
            products: [],
            pagination: {},
            breadcrumbs: [],
            filters: {
                priceMin: '',
                priceMax: '',
                availability: '',
                discount: '',
                wholesale: '',
                new: '',
            },
            syncingQuery: false,
            suppressLiveApply: true,
            applyTimer: null,
        };
    },
    computed: {
        spaMode() {
            return Boolean(this.$router);
        },
        totalCount() {
            return this.pagination.total ?? this.products.length ?? 0;
        },
        categoryTree() {
            if (Array.isArray(this.config.categoryTree) && this.config.categoryTree.length) {
                return this.config.categoryTree;
            }
            return (this.config.categories || []).map((c) => ({ ...c, children: c.children || [] }));
        },
        currentCategoryUrl() {
            if (this.spaMode && this.$route.name === 'category') {
                return this.$route.params.category || '';
            }
            return this.config.currentCategory?.url || '';
        },
        currentCategoryName() {
            if (!this.currentCategoryUrl) return '';
            return this.findCategoryInTree(this.categoryTree, this.currentCategoryUrl)?.name
                || this.config.currentCategory?.name
                || this.currentCategoryUrl;
        },
        visiblePages() {
            const current = this.pagination.current_page || 1;
            const last = this.pagination.last_page || 1;
            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);
            const pages = [];

            if (start > 1) {
                pages.push({ type: 'page', num: 1, key: 'p1' });
                if (start > 2) pages.push({ type: 'dots', key: 'dots-start' });
            }

            for (let i = start; i <= end; i++) {
                pages.push({
                    type: i === current ? 'current' : 'page',
                    num: i,
                    key: `p${i}`,
                });
            }

            if (end < last) {
                if (end < last - 1) pages.push({ type: 'dots', key: 'dots-end' });
                pages.push({ type: 'page', num: last, key: `p${last}` });
            }

            return pages;
        },
        activeFiltersCount() {
            let count = 0;
            if (this.filters.priceMin) count++;
            if (this.filters.priceMax) count++;
            if (this.currentCategoryUrl) count++;
            if (this.filters.availability && this.filters.availability !== '1') count++;
            if (this.filters.discount === '1') count++;
            if (this.filters.wholesale === '1') count++;
            if (this.filters.new === '1') count++;
            return count;
        },
        activeFilterTags() {
            const tags = [];
            if (this.filters.priceMin || this.filters.priceMax) {
                const min = this.filters.priceMin || '0';
                const max = this.filters.priceMax || '∞';
                tags.push({ key: 'price', label: `${min}–${max} ₴` });
            }
            if (this.currentCategoryUrl) {
                tags.push({ key: 'category', label: this.currentCategoryName });
            }
            if (this.filters.availability === 'out') {
                tags.push({ key: 'availability', label: 'Немає в наявності' });
            } else if (this.filters.availability === 'all') {
                tags.push({ key: 'availability', label: 'Всі товари' });
            }
            if (this.filters.discount === '1') {
                tags.push({ key: 'discount', label: 'Зі знижкою' });
            }
            if (this.filters.wholesale === '1') {
                tags.push({ key: 'wholesale', label: 'Опт' });
            }
            if (this.filters.new === '1') {
                tags.push({ key: 'new', label: 'Новинки' });
            }
            return tags;
        },
    },
    watch: {
        config: {
            deep: true,
            handler(next) {
                this.hydrateFromConfig(next);
            },
        },
        filters: {
            deep: true,
            handler() {
                this.scheduleLiveApply();
            },
        },
        sort() {
            this.scheduleLiveApply();
        },
        '$route.query': {
            deep: true,
            handler() {
                if (!this.spaMode || this.syncingQuery) return;
                this.suppressLiveApply = true;
                this.readUrlParams();
                const page = parseInt(this.$route.query.page || '1', 10);
                this.fetchProducts(page, false).finally(() => {
                    this.$nextTick(() => { this.suppressLiveApply = false; });
                });
            },
        },
    },
    mounted() {
        this.hydrateFromConfig(this.config);
        this.readUrlParams();
        this.$nextTick(() => { this.suppressLiveApply = false; });
        if (!this.spaMode) {
            window.addEventListener('popstate', this.handlePopState);
        }
    },
    unmounted() {
        if (this.applyTimer) clearTimeout(this.applyTimer);
        if (!this.spaMode) {
            window.removeEventListener('popstate', this.handlePopState);
        }
    },
    methods: {
        hydrateFromConfig(config = {}) {
            if (Array.isArray(config.products)) {
                this.products = [...config.products];
            }
            if (config.pagination && typeof config.pagination === 'object') {
                this.pagination = { ...config.pagination };
            }
            if (Array.isArray(config.breadcrumbs)) {
                this.breadcrumbs = [...config.breadcrumbs];
            }
        },
        scheduleLiveApply() {
            if (this.suppressLiveApply) return;
            if (this.applyTimer) clearTimeout(this.applyTimer);
            this.applyTimer = setTimeout(() => {
                this.fetchProducts(1, true, false);
            }, 280);
        },
        readUrlParams() {
            const source = this.spaMode
                ? this.$route.query
                : Object.fromEntries(new URLSearchParams(window.location.search).entries());

            this.filters.priceMin = source.price_min || '';
            this.filters.priceMax = source.price_max || '';
            this.filters.availability = source.availability || '';
            this.filters.discount = source.discount || '';
            this.filters.wholesale = source.wholesale || '';
            this.filters.new = source.new || '';
            this.sort = source.sort || 'default';
        },
        buildParams(page = 1) {
            const params = new URLSearchParams();
            if (this.filters.priceMin) params.set('price_min', this.filters.priceMin);
            if (this.filters.priceMax) params.set('price_max', this.filters.priceMax);
            if (this.filters.availability) params.set('availability', this.filters.availability);
            if (this.filters.discount) params.set('discount', this.filters.discount);
            if (this.filters.wholesale) params.set('wholesale', this.filters.wholesale);
            if (this.filters.new) params.set('new', this.filters.new);
            if (this.sort && this.sort !== 'default') params.set('sort', this.sort);
            if (page > 1) params.set('page', String(page));
            return params;
        },
        buildApiUrl(page = 1) {
            const apiBase = this.config.spaApiUrl || '/api/spa/catalog';
            const params = this.buildParams(page);
            const query = params.toString();
            return query ? `${apiBase}?${query}` : apiBase;
        },
        buildBrowserUrl(page = 1) {
            const base = this.config.filterBaseUrl || window.location.pathname;
            const params = this.buildParams(page);
            const query = params.toString();
            return query ? `${base}?${query}` : base;
        },
        buildBrowserQuery(page = 1) {
            const params = this.buildParams(page);
            const query = {};
            params.forEach((value, key) => {
                query[key] = value;
            });
            return query;
        },
        handlePopState() {
            this.suppressLiveApply = true;
            this.readUrlParams();
            const page = parseInt(new URLSearchParams(window.location.search).get('page') || '1', 10);
            this.fetchProducts(page, false).finally(() => {
                this.$nextTick(() => { this.suppressLiveApply = false; });
            });
        },
        goToPage(page) {
            if (page < 1 || page > (this.pagination.last_page || 1) || page === this.pagination.current_page) {
                return;
            }
            this.fetchProducts(page, true, true);
        },
        fetchProducts(page = 1, pushState = true, scrollToList = false) {
            this.loading = true;
            spaState.pageBusy = true;
            const fetchUrl = this.buildApiUrl(page);

            return fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            })
                .then((response) => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then((data) => {
                    if (!data.success) throw new Error(data.message || 'Filter error');

                    const payload = data.data && typeof data.data === 'object' ? data.data : data;

                    this.products = Array.isArray(payload.products) ? payload.products : [];
                    this.pagination = payload.pagination && typeof payload.pagination === 'object'
                        ? { ...payload.pagination }
                        : {};

                    if (Array.isArray(payload.breadcrumbs)) {
                        this.breadcrumbs = [...payload.breadcrumbs];
                    }

                    if (payload.hero && this.config.hero) {
                        Object.assign(this.config.hero, payload.hero);
                    } else if (this.config.hero && this.pagination.total != null) {
                        this.config.hero.stat = this.pagination.total;
                    }

                    if (payload.currentCategory) {
                        this.config.currentCategory = payload.currentCategory;
                    }

                    if (pushState) {
                        if (this.spaMode) {
                            this.syncingQuery = true;
                            this.$router.replace({
                                name: this.$route.name,
                                params: { ...this.$route.params },
                                query: this.buildBrowserQuery(page),
                            }).finally(() => {
                                this.syncingQuery = false;
                            });
                        } else {
                            window.history.replaceState({ catalogPage: page }, '', this.buildBrowserUrl(page));
                        }
                    }

                    if (scrollToList) {
                        this.$nextTick(() => {
                            if (isScrollRestoring) return;
                            document.getElementById('catalog-products-anchor')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start',
                            });
                        });
                    }
                })
                .catch(() => {
                    window.$toast?.error?.('Не вдалося завантажити товари. Спробуйте ще раз.');
                })
                .finally(() => {
                    this.loading = false;
                    spaState.pageBusy = false;
                });
        },
        navigateToCategory(url) {
            const query = this.buildBrowserQuery(1);
            if (!this.spaMode) {
                const href = url
                    ? `/catalog/categoriya/${encodeURIComponent(url)}${Object.keys(query).length ? `?${new URLSearchParams(query)}` : ''}`
                    : `/catalog${Object.keys(query).length ? `?${new URLSearchParams(query)}` : ''}`;
                window.location.href = href;
                return;
            }

            if (!url) {
                this.$router.push({ name: 'catalog', query });
                return;
            }

            this.$router.push({
                name: 'category',
                params: { category: url },
                query,
            });
        },
        onCrumbClick(event, url) {
            if (!this.spaMode || !url) return;
            try {
                const parsed = new URL(url, window.location.origin);
                if (parsed.origin !== window.location.origin) return;
                event.preventDefault();
                this.$router.push(parsed.pathname + parsed.search);
            } catch {
                // keep default navigation
            }
        },
        closeFilters() {
            this.filtersOpen = false;
            document.body.style.overflow = '';
        },
        openFilters() {
            this.filtersOpen = true;
            document.body.style.overflow = 'hidden';
        },
        resetFilters() {
            this.suppressLiveApply = true;
            this.filters = {
                priceMin: '',
                priceMax: '',
                availability: '',
                discount: '',
                wholesale: '',
                new: '',
            };
            this.sort = 'default';
            this.$nextTick(() => {
                this.suppressLiveApply = false;
                if (this.currentCategoryUrl) {
                    this.navigateToCategory('');
                } else {
                    this.fetchProducts(1, true, false);
                }
            });
        },
        removeFilter(key) {
            if (key === 'category') {
                this.navigateToCategory('');
                return;
            }
            this.suppressLiveApply = true;
            if (key === 'price') {
                this.filters.priceMin = '';
                this.filters.priceMax = '';
            } else {
                this.filters[key] = '';
            }
            this.$nextTick(() => {
                this.suppressLiveApply = false;
                this.fetchProducts(1, true, false);
            });
        },
        findCategoryInTree(nodes, url) {
            for (const node of nodes || []) {
                if (node.url === url) return node;
                const found = this.findCategoryInTree(node.children || [], url);
                if (found) return found;
            }
            return null;
        },
    },
};
</script>
