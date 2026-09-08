@php
    $href = route('catalog_product_page', [
        'category' => $product->category_url ?? 'catalog',
        'product' => $product->url,
    ]);
    $price = $product->price;
    $hasPrice = $price !== null && $price !== '' && (float) $price > 0;
    $priceLabel = $hasPrice
        ? number_format((float) $price, 0, '.', ' ').' ₴'
        : 'Ціна уточнюється';
    $variant = $variant ?? 'grid';
@endphp

<a href="{{ $href }}" class="mega-tile mega-tile--{{ $variant }}" title="{{ $product->name }}">
    <span class="mega-tile__media" aria-hidden="true">
        <img
            src="{{ $product->getImagePath() }}"
            alt=""
            width="80"
            height="80"
            loading="lazy"
            decoding="async"
        >
    </span>
    <span class="mega-tile__body">
        <span class="mega-tile__name">{{ $product->name }}</span>
        <span class="mega-tile__price {{ $hasPrice ? '' : 'mega-tile__price--muted' }}">{{ $priceLabel }}</span>
    </span>
</a>
