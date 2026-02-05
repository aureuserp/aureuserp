<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ViewCategory;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewProductCategory extends ViewCategory
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ChatterAction::make()
                ->resource(static::$resource),
            ...parent::getHeaderActions(),
        ];
    }
}
