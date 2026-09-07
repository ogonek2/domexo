<?php

namespace App\Filament\Widgets;

use App\Services\ShopAnalytics;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TopProductsWidget extends TableWidget
{
    protected static ?int $sort = -16;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $rows = ShopAnalytics::topProducts(12);

        return $table
            ->heading('Популярные товары')
            ->description('По позициям в заказах (топ-12)')
            ->paginated(false)
            ->records(fn (): Collection => $rows->values())
            ->columns([
                TextColumn::make('name')
                    ->label('Товар')
                    ->wrap()
                    ->weight('bold'),
                TextColumn::make('articule')
                    ->label('Артикул')
                    ->toggleable(),
                TextColumn::make('qty')
                    ->label('Продано шт.')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                TextColumn::make('orders')
                    ->label('В заказах')
                    ->alignCenter(),
                TextColumn::make('revenue')
                    ->label('Выручка')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0, '.', ' ').' ₴')
                    ->weight('bold')
                    ->alignEnd(),
            ]);
    }
}
