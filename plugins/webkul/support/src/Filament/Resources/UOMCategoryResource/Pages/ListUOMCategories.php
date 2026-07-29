<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Support\Filament\Resources\UOMCategoryResource;

class ListUOMCategories extends ListRecords
{
    protected static string $resource = UOMCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/uom-category/pages/list-uom-categories.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
