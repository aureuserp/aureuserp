<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category/pages/list-categories.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
