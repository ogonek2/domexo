<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Orders;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected function getStats(): array
    {
        $total = Product::query()->count();
        $inStock = Product::query()->where('availability', 'in_stock')->count();
        $discounted = Product::query()->where('discount', '>', 0)->count();
        $withoutImage = Product::query()
            ->where(fn ($query) => $query->whereNull('image_path')->orWhere('image_path', ''))
            ->count();

        return [
            Stat::make('Товаров', (string) $total)
                ->description($inStock . ' в наличии, ' . ($total - $inStock) . ' нет')
                ->descriptionIcon('heroicon-m-cube')
                ->color($inStock > 0 ? 'success' : 'danger'),

            Stat::make('Со скидкой', (string) $discounted)
                ->description('Товары с ненулевой скидкой')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),

            Stat::make('Без фото', (string) $withoutImage)
                ->description('Требуют загрузки изображения')
                ->descriptionIcon('heroicon-m-photo')
                ->color($withoutImage > 0 ? 'danger' : 'success'),

            Stat::make('Категорий', (string) Category::query()->count())
                ->description('Активных: ' . Category::query()->where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),

            Stat::make('Заказов', (string) Orders::query()->count())
                ->description('За 7 дней: ' . Orders::query()->where('created_at', '>=', now()->subDays(7))->count())
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
