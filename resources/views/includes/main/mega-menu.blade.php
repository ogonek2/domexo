@php
    $megaMenuItems = $megaMenuItems ?? get_mega_menu_data();
@endphp

<div id="megaMenu" class="mega-menu" aria-hidden="true">
    <div class="mega-menu__inner max-w-site mx-auto">
        <div class="mega-menu__layout">
            <ul class="mega-menu__cats" role="list">
                @foreach ($megaMenuItems as $index => $item)
                    <li>
                        <button type="button"
                                class="mega-menu__cat {{ $index === 0 ? 'mega-menu__cat--active' : '' }}"
                                data-mega-panel="mega-panel-{{ $item['category']->id }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <span class="mega-menu__cat-name">{{ $item['category']->name }}</span>
                            @if ($item['count'] > 0)
                                <span class="mega-menu__cat-count">{{ $item['count'] }}</span>
                            @endif
                            <x-lucide-icon name="chevron-right" width="14" class="mega-menu__cat-arrow" />
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="mega-menu__panels">
                @foreach ($megaMenuItems as $index => $item)
                    <div id="mega-panel-{{ $item['category']->id }}"
                         class="mega-menu__panel {{ $index === 0 ? 'mega-menu__panel--active' : '' }}"
                         role="region">
                        <div class="mega-menu__panel-head">
                            <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                               class="mega-menu__panel-title">
                                {{ $item['category']->name }}
                                <x-lucide-icon name="arrow-right" width="16" />
                            </a>
                            @if ($item['count'] > 0)
                                <span class="mega-menu__panel-meta">{{ $item['count'] }} товарів</span>
                            @endif
                        </div>

                        @if (!empty($item['children']))
                            <div class="mega-menu__section">
                                <p class="mega-menu__section-label">Підкатегорії</p>
                                <div class="mega-menu__tile-grid">
                                    @foreach ($item['children'] as $block)
                                        @include('includes.main.mega-menu-category', ['block' => $block])
                                    @endforeach
                                    @foreach ($item['children'] as $block)
                                        @foreach ($block['children'] ?? [] as $sub)
                                            @include('includes.main.mega-menu-category', ['block' => $sub])
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($item['products']->isNotEmpty())
                            <div class="mega-menu__section {{ !empty($item['children']) ? 'mega-menu__section--spaced' : '' }}">
                                @if (!empty($item['children']))
                                    <p class="mega-menu__section-label">Товари</p>
                                @endif
                                <div class="mega-menu__tile-grid">
                                    @foreach ($item['products'] as $product)
                                        @include('includes.main.mega-menu-product', ['product' => $product, 'variant' => 'grid'])
                                    @endforeach
                                </div>
                                @if ($item['count'] > $item['products']->count())
                                    <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                                       class="mega-menu__more mega-menu__more--block">
                                        Усі {{ $item['count'] }} товарів
                                        <x-lucide-icon name="arrow-right" width="14" />
                                    </a>
                                @endif
                            </div>
                        @elseif (empty($item['children']) && $item['count'] > 0)
                            <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                               class="mega-menu__more mega-menu__more--block">
                                Переглянути всі {{ $item['count'] }} товарів
                                <x-lucide-icon name="arrow-right" width="14" />
                            </a>
                        @elseif (empty($item['children']))
                            <p class="mega-menu__empty">Товари скоро з'являться</p>
                            <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                               class="mega-menu__more mega-menu__more--block">
                                Перейти до категорії
                                <x-lucide-icon name="arrow-right" width="14" />
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mega-menu__footer">
            <a href="{{ route('catalog') }}" class="mega-menu__footer-link">
                <x-lucide-icon name="layout-grid" width="16" />
                Весь каталог
            </a>
            <a href="{{ route('catalog') }}?discount=1" class="mega-menu__footer-link">
                <x-lucide-icon name="tag" width="16" />
                Акції
            </a>
            <a href="{{ route('catalog') }}?new=1" class="mega-menu__footer-link">
                <x-lucide-icon name="star" width="16" />
                Новинки
            </a>
            <a href="{{ route('catalog') }}?wholesale=1" class="mega-menu__footer-link">
                <x-lucide-icon name="boxes" width="16" />
                Опт
            </a>
        </div>
    </div>
</div>

<div id="megaMenuOverlay" class="mega-menu-overlay" aria-hidden="true"></div>
