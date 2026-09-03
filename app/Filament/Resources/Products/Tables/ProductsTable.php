<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Support\ShopOptions;
use App\Models\Product;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Фото')
                    ->getStateUsing(fn (Product $record): string => $record->getImagePath())
                    ->checkFileExistence(false)
                    ->square()
                    ->size(56),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(70)
                    ->description(fn (Product $record): ?string => $record->articule ? "Арт. {$record->articule}" : null),

                TextColumn::make('price')
                    ->label('Цена')
                    ->sortable()
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' ₴'),

                TextColumn::make('discount')
                    ->label('Скидка')
                    ->badge()
                    ->sortable()
                    ->color(fn (?int $state): string => $state > 0 ? 'warning' : 'gray')
                    ->formatStateUsing(fn (?int $state): string => $state > 0 ? "{$state}%" : '—'),

                TextColumn::make('availability')
                    ->label('Наличие')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => $state === 'in_stock' ? 'success' : 'danger')
                    ->formatStateUsing(fn (?string $state): string => ShopOptions::AVAILABILITY[$state] ?? (string) $state),

                IconColumn::make('is_wholesale')
                    ->label('Опт')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('categories_count')
                    ->counts('categories')
                    ->label('Категорий')
                    ->badge()
                    ->color('info'),

                TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Фото')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('brand')
                        ->label('Бренд')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('articule')
                        ->label('Артикул')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('external_id')
                        ->label('Внешний ID')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('country')
                        ->label('Страна')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('weight')
                        ->label('Вес, кг')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('characteristics')
                        ->label('Характеристик')
                        ->badge()
                        ->color('info')
                        ->state(fn (Product $record): int => count($record->characteristicsList()))
                        ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('availability')
                    ->label('Наличие')
                    ->options(ShopOptions::AVAILABILITY),

                TernaryFilter::make('is_wholesale')
                    ->label('Оптовый товар'),

                SelectFilter::make('categories')
                    ->label('Категория')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('catalogs')
                    ->label('Каталог')
                    ->relationship('catalogs', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('brand')
                    ->label('Бренд')
                    ->options(fn (): array => Product::query()
                        ->whereNotNull('brand')
                        ->distinct()
                        ->orderBy('brand')
                        ->pluck('brand', 'brand')
                        ->all())
                    ->searchable(),

                Filter::make('has_discount')
                    ->label('Со скидкой')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('discount', '>', 0)),

                    SelectFilter::make('country')
                        ->label('Страна')
                        ->options(fn (): array => Product::query()
                            ->whereNotNull('country')
                            ->where('country', '!=', '')
                            ->distinct()
                            ->orderBy('country')
                            ->pluck('country', 'country')
                            ->all())
                        ->searchable(),

                    Filter::make('without_image')
                        ->label('Без фото')
                        ->toggle()
                        ->query(fn (Builder $query): Builder => $query->where(
                            fn (Builder $query) => $query->whereNull('image_path')->orWhere('image_path', ''),
                        )),

                    Filter::make('without_category')
                        ->label('Без категории')
                        ->toggle()
                        ->query(fn (Builder $query): Builder => $query->whereDoesntHave('categories')),

                    Filter::make('without_characteristics')
                        ->label('Без характеристик')
                        ->toggle()
                        ->query(fn (Builder $query): Builder => $query->where(
                            fn (Builder $query) => $query
                                ->whereNull('characteristics')
                                ->orWhereJsonLength('characteristics', 0),
                        )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Дублировать')
                    ->excludeAttributes(['url'])
                    ->beforeReplicaSaved(function (Product $replica): void {
                        $replica->name = $replica->name . ' (копия)';
                        $replica->articule = $replica->articule ? $replica->articule . '-copy' : null;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setAvailability')
                        ->label('Изменить наличие')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Select::make('availability')
                                ->label('Наличие')
                                ->options(ShopOptions::AVAILABILITY)
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            self::updateWithoutFeedRebuild($records, ['availability' => $data['availability']]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('setDiscount')
                        ->label('Изменить скидку')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            TextInput::make('discount')
                                ->label('Скидка, %')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            self::updateWithoutFeedRebuild($records, ['discount' => (int) $data['discount']]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Массовое обновление без событий модели: иначе каждая запись
     * заново собирает URL и фид товаров.
     *
     * @param  Collection<int, Product>  $records
     * @param  array<string, mixed>  $attributes
     */
    protected static function updateWithoutFeedRebuild(Collection $records, array $attributes): void
    {
        Product::withoutEvents(function () use ($records, $attributes): void {
            Product::query()->whereKey($records->modelKeys())->update($attributes);
        });
    }
}
