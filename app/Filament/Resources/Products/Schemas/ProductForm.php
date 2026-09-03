<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\Components\ShopImageUpload;
use App\Filament\Support\ShopOptions;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Товар')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Основное')
                            ->icon('heroicon-o-cube')
                            ->schema(self::mainFields()),

                        Tab::make('Цены')
                            ->icon('heroicon-o-banknotes')
                            ->schema(self::priceFields()),

                        Tab::make('Изображения')
                            ->icon('heroicon-o-photo')
                            ->schema(self::imageFields()),

                        Tab::make('Характеристики')
                            ->icon('heroicon-o-list-bullet')
                            ->schema(self::characteristicsFields()),

                        Tab::make('Связи')
                            ->icon('heroicon-o-rectangle-group')
                            ->schema(self::relationFields()),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema(self::seoFields()),
                    ]),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    protected static function mainFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Название')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            TextInput::make('articule')
                ->label('Артикул')
                ->maxLength(255),

            TextInput::make('brand')
                ->label('Бренд')
                ->maxLength(255)
                ->datalist(fn () => Product::query()
                    ->whereNotNull('brand')
                    ->distinct()
                    ->orderBy('brand')
                    ->pluck('brand')
                    ->all()),

            Select::make('availability')
                ->label('Наличие')
                ->options(ShopOptions::AVAILABILITY)
                ->default('in_stock')
                ->required()
                ->native(false)
                ->helperText('Товары не «в наличии» скрыты из каталога витрины.'),

            Select::make('condition_item')
                ->label('Состояние')
                ->options(ShopOptions::CONDITION)
                ->default('new')
                ->native(false),

            TextInput::make('url')
                ->label('URL (ЧПУ)')
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull()
                ->helperText('Генерируется автоматически из названия при каждом сохранении.'),

            RichEditor::make('description')
                ->label('Описание')
                ->columnSpanFull(),

            Textarea::make('complectation')
                ->label('Комплектация')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function priceFields(): array
    {
        return [
            TextInput::make('price')
                ->label('Цена')
                ->numeric()
                ->minValue(0)
                ->required()
                ->suffix('₴'),

            TextInput::make('discount')
                ->label('Скидка')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(0)
                ->suffix('%'),

            TextInput::make('unit_name')
                ->label('Единица измерения')
                ->default('шт')
                ->maxLength(50),

            TextInput::make('unit_name_plural')
                ->label('Единица (мн. число)')
                ->maxLength(50),

            Toggle::make('is_wholesale')
                ->label('Оптовый товар')
                ->live()
                ->columnSpanFull(),

            TextInput::make('wholesale_price')
                ->label('Оптовая цена')
                ->numeric()
                ->minValue(0)
                ->suffix('₴')
                ->visible(fn (Get $get): bool => (bool) $get('is_wholesale'))
                ->requiredIf('is_wholesale', true),

            TextInput::make('wholesale_min_quantity')
                ->label('Минимальный опт. заказ')
                ->numeric()
                ->minValue(1)
                ->visible(fn (Get $get): bool => (bool) $get('is_wholesale')),

            TextInput::make('units_per_box')
                ->label('Единиц в упаковке')
                ->numeric()
                ->minValue(1)
                ->visible(fn (Get $get): bool => (bool) $get('is_wholesale')),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function imageFields(): array
    {
        return [
            ShopImageUpload::make('image_path')
                ->label('Главное изображение')
                ->columnSpanFull()
                ->helperText('Загружается на BunnyCDN, если он настроен, иначе в локальное хранилище.'),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function characteristicsFields(): array
    {
        return [
            KeyValue::make('characteristics')
                ->label('Характеристики')
                ->keyLabel('Название')
                ->valueLabel('Значение')
                ->reorderable()
                ->columnSpanFull()
                ->helperText(fn (?Product $record): string => self::templateHint($record)),

            KeyValue::make('modifications')
                ->label('Модификации')
                ->keyLabel('Название')
                ->valueLabel('Значение')
                ->columnSpanFull(),

            KeyValue::make('additional_fields')
                ->label('Дополнительные поля')
                ->keyLabel('Название')
                ->valueLabel('Значение')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function relationFields(): array
    {
        return [
            Select::make('categories')
                ->label('Категории')
                ->relationship('categories', 'name')
                ->getOptionLabelFromRecordUsing(fn (Category $record): string => ShopOptions::categoryLabel($record->id, $record->name))
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            Select::make('catalogs')
                ->label('Каталоги')
                ->relationship('catalogs', 'name')
                ->getOptionLabelFromRecordUsing(fn (Catalog $record): string => ShopOptions::catalogLabel($record->id, $record->name))
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            Select::make('packages')
                ->label('Упаковки / доп. блоки')
                ->relationship('packages', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function seoFields(): array
    {
        return [
            TextInput::make('seo_title')
                ->label('SEO заголовок')
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('seo_description')
                ->label('SEO описание')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('seo_keywords')
                ->label('SEO ключевые слова')
                ->rows(2)
                ->columnSpanFull(),
        ];
    }

    /**
     * Подсказка со списком характеристик из шаблона категории/каталога —
     * их витрина показывает, если у товара нет своих.
     */
    protected static function templateHint(?Product $record): string
    {
        if (! $record) {
            return 'Характеристики попадают в карточку товара как есть.';
        }

        $names = collect($record->getTemplateCharacteristics())
            ->map(fn ($characteristic) => is_array($characteristic)
                ? ($characteristic['name'] ?? $characteristic['key'] ?? null)
                : $characteristic)
            ->filter()
            ->implode(', ');

        return $names === ''
            ? 'Характеристики попадают в карточку товара как есть.'
            : "Шаблон предлагает: {$names}.";
    }
}
