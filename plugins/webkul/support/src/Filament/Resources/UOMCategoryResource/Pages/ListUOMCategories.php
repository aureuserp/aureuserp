<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Support\Filament\Resources\UOMCategoryResource;
use Webkul\Support\Models\UOMCategory;

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
