<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Orders;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrdersInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заказ')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Номер'),

                        TextEntry::make('created_at')
                            ->label('Создан')
                            ->dateTime('d.m.Y H:i'),

                        TextEntry::make('total_price')
                            ->label('Сумма')
                            ->getStateUsing(fn (Orders $record): string => $record->formatted_total_price)
                            ->weight('bold')
                            ->size('lg'),
                    ]),

                Section::make('Покупатель')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Имя')
                            ->placeholder('—'),

                        TextEntry::make('lastname')
                            ->label('Фамилия')
                            ->placeholder('—'),

                        TextEntry::make('fathername')
                            ->label('Отчество')
                            ->placeholder('—'),

                        TextEntry::make('phone')
                            ->label('Телефон')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('comment')
                            ->label('Комментарий')
                            ->columnSpan(2)
                            ->placeholder('—'),
                    ]),

                Section::make('Доставка и оплата')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('delivery_service')
                            ->label('Служба доставки')
                            ->placeholder('—'),

                        TextEntry::make('payment')
                            ->label('Способ оплаты')
                            ->placeholder('—'),

                        TextEntry::make('city')
                            ->label('Город')
                            ->placeholder('—'),

                        TextEntry::make('warehouse')
                            ->label('Отделение')
                            ->placeholder('—'),

                        TextEntry::make('manual_address')
                            ->label('Адрес вручную')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),

                Section::make('Состав заказа')
                    ->schema([
                        RepeatableEntry::make('cart_items')
                            ->hiddenLabel()
                            ->columns(4)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Товар')
                                    ->columnSpan(2),

                                TextEntry::make('quantity')
                                    ->label('Кол-во'),

                                TextEntry::make('price')
                                    ->label('Цена')
                                    ->numeric(decimalPlaces: 2)
                                    ->suffix(' ₴'),
                            ])
                            ->placeholder('Не удалось прочитать состав корзины'),
                    ]),
            ]);
    }
}
