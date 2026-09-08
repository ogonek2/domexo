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

                        @if ($item['products']->isNotEmpty())
                            <ul class="mega-menu__products mega-menu__products--flat">
                                @foreach ($item['products'] as $product)
                                    <li>
                                        <a href="{{ route('catalog_product_page', ['category' => $product->category_url, 'product' => $product->url]) }}">
                                            {{ $product->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($item['count'] > $item['products']->count())
                                <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                                   class="mega-menu__more mega-menu__more--block">
                                    Переглянути всі {{ $item['count'] }} товарів
                                    <x-lucide-icon name="arrow-right" width="14" />
                                </a>
                            @endif
                        @endif

                        @if (!empty($item['children']))
                            <div class="mega-menu__groups">
                                @foreach ($item['children'] as $block)
                                    <div class="mega-menu__group">
                                        <a href="{{ route('catalog_category_page', $block['category']->url) }}"
                                           class="mega-menu__group-title">
                                            {{ $block['category']->name }}
                                            @if ($block['count'] > 0)
                                                <span class="mega-menu__group-count">{{ $block['count'] }}</span>
                                            @endif
                                        </a>

                                        @if (!empty($block['children']))
                                            @foreach ($block['children'] as $sub)
                                                <div class="mega-menu__subgroup">
                                                    <a href="{{ route('catalog_category_page', $sub['category']->url) }}"
                                                       class="mega-menu__subgroup-title">
                                                        {{ $sub['category']->name }}
                                                    </a>
                                                    @if ($sub['products']->isNotEmpty())
                                                        <ul class="mega-menu__products">
                                                            @foreach ($sub['products'] as $product)
                                                                <li>
                                                                    <a href="{{ route('catalog_product_page', ['category' => $product->category_url, 'product' => $product->url]) }}">
                                                                        {{ $product->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    <a href="{{ route('catalog_category_page', $sub['category']->url) }}"
                                                       class="mega-menu__more">
                                                        Всі товари
                                                        <x-lucide-icon name="arrow-right" width="12" />
                                                    </a>
                                                </div>
                                            @endforeach
                                        @elseif ($block['products']->isNotEmpty())
                                            <ul class="mega-menu__products">
                                                @foreach ($block['products'] as $product)
                                                    <li>
                                                        <a href="{{ route('catalog_product_page', ['category' => $product->category_url, 'product' => $product->url]) }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if ($block['count'] > $block['products']->count())
                                                <a href="{{ route('catalog_category_page', $block['category']->url) }}"
                                                   class="mega-menu__more">
                                                    Всі {{ $block['count'] }} товарів
                                                    <x-lucide-icon name="arrow-right" width="12" />
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('catalog_category_page', $block['category']->url) }}"
                                               class="mega-menu__more">
                                                Перейти
                                                <x-lucide-icon name="arrow-right" width="12" />
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($item['products']->isEmpty() && $item['count'] > 0)
                            <a href="{{ route('catalog_category_page', $item['category']->url) }}"
                               class="mega-menu__more mega-menu__more--block">
                                Переглянути всі {{ $item['count'] }} товарів
                                <x-lucide-icon name="arrow-right" width="14" />
                            </a>
                        @elseif ($item['products']->isEmpty())
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
