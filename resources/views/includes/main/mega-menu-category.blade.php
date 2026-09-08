@php
    $category = $block['category'];
    $count = (int) ($block['count'] ?? 0);
    $href = route('catalog_category_page', $category->url);

    $image = null;
    if (! empty($category->meta_image)) {
        $raw = (string) $category->meta_image;
        $image = str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')
            ? $raw
            : asset('storage/'.ltrim($raw, '/'));
    } elseif (! empty($block['products']) && $block['products']->isNotEmpty()) {
        $image = $block['products']->first()->getImagePath();
    } else {
        $image = asset('dist/img/no-image.png');
    }

    $countLabel = $count > 0
        ? $count.' '.($count === 1 ? 'товар' : ($count < 5 ? 'товари' : 'товарів'))
        : 'Перейти';
@endphp

<a href="{{ $href }}" class="mega-tile mega-tile--grid mega-tile--category" title="{{ $category->name }}">
    <span class="mega-tile__media" aria-hidden="true">
        <img
            src="{{ $image }}"
            alt=""
            width="80"
            height="80"
            loading="lazy"
            decoding="async"
        >
    </span>
    <span class="mega-tile__body">
        <span class="mega-tile__name">{{ $category->name }}</span>
        <span class="mega-tile__price mega-tile__price--muted">{{ $countLabel }}</span>
    </span>
</a>
