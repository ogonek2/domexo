<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Orders;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class OrdersInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заказ')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('id')->label('Номер'),
                        TextEntry::make('created_at')->label('Создан')->dateTime('d.m.Y H:i'),
                        TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Orders::STATUSES[$state ?? Orders::STATUS_NEW] ?? (string) $state)
                            ->color(fn (?string $state): string => match ($state) {
                                Orders::STATUS_SHIPPED, Orders::STATUS_POSTED => 'info',
                                Orders::STATUS_DELIVERED => 'success',
                                Orders::STATUS_CANCELLED => 'danger',
                                Orders::STATUS_ASSEMBLED => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('tracking_number')
                            ->label('Накладная')
                            ->placeholder('—')
                            ->copyable(),
                        TextEntry::make('total_price')
                            ->label('Сумма')
                            ->getStateUsing(fn (Orders $record): string => $record->formatted_total_price)
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->columnSpan(2),
                    ]),

                Section::make('Покупатель')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')->label('Имя')->placeholder('—'),
                        TextEntry::make('lastname')->label('Фамилия')->placeholder('—'),
                        TextEntry::make('fathername')->label('Отчество')->placeholder('—'),
                        TextEntry::make('phone')->label('Телефон')->copyable()->placeholder('—'),
                        TextEntry::make('email')->label('Email')->copyable()->placeholder('—'),
                        TextEntry::make('comment')->label('Комментарий')->columnSpanFull()->placeholder('—'),
                    ]),

                Section::make('Доставка и оплата')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('delivery_service')->label('Служба доставки')->placeholder('—'),
                        TextEntry::make('payment')->label('Способ оплаты')->placeholder('—'),
                        TextEntry::make('city')->label('Город')->placeholder('—'),
                        TextEntry::make('warehouse')->label('Отделение')->placeholder('—'),
                        TextEntry::make('manual_address')->label('Адрес вручную')->columnSpanFull()->placeholder('—'),
                    ]),

                Section::make('Состав заказа (для сборки)')
                    ->description('Полный список позиций для склада')
                    ->schema([
                        RepeatableEntry::make('cart_items')
                            ->hiddenLabel()
                            ->columns(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('Фото')
                                    ->getStateUsing(function (array $state): ?string {
                                        $img = $state['image'] ?? $state['image_path'] ?? null;
                                        if (! is_string($img) || $img === '') {
                                            return null;
                                        }
                                        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '//')) {
                                            return $img;
                                        }

                                        return asset('storage/'.ltrim($img, '/'));
                                    })
                                    ->height(64)
                                    ->square()
                                    ->columnSpan(2),

                                TextEntry::make('articule')
                                    ->label('Артикул')
                                    ->placeholder('—')
                                    ->copyable()
                                    ->columnSpan(2),

                                TextEntry::make('name')
                                    ->label('Товар')
                                    ->columnSpan(4),

                                TextEntry::make('quantity')
                                    ->label('Кол-во')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->columnSpan(1),

                                TextEntry::make('price')
                                    ->label('Цена')
                                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 0, '.', ' ').' ₴')
                                    ->columnSpan(1),

                                TextEntry::make('line_total')
                                    ->label('Сумма')
                                    ->getStateUsing(function (array $state): string {
                                        $qty = (int) ($state['quantity'] ?? 1);
                                        $price = (float) ($state['price'] ?? 0);

                                        return number_format($qty * $price, 0, '.', ' ').' ₴';
                                    })
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ])
                            ->placeholder('Не удалось прочитать состав корзины'),
                    ]),
            ]);
    }
}
