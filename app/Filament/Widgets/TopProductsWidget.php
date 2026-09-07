<?php

namespace App\Filament\Widgets;

use App\Services\ShopAnalytics;
use Filament\Widgets\Widget;

class TopProductsWidget extends Widget
{
    protected static ?int $sort = -16;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.top-products';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'rows' => ShopAnalytics::topProducts(12),
            'categories' => ShopAnalytics::topCategories(8),
            'abandoned' => ShopAnalytics::abandonedCarts(),
        ];
    }
}
