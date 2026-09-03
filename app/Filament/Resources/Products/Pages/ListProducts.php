<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Actions\ProductExportAction;
use App\Filament\Resources\Products\Actions\ProductImportAction;
use App\Filament\Resources\Products\Actions\ProductTemplateAction;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                ProductImportAction::make(),
                ProductExportAction::make(),
                ProductTemplateAction::make(),
            ])
                ->label('Импорт и экспорт')
                ->icon(Heroicon::OutlinedArrowsUpDown)
                ->button()
                ->color('gray'),

            CreateAction::make(),
        ];
    }
}
