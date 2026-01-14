<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ViewCategory;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewProductCategory extends ViewCategory
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            ...parent::getHeaderActions(),
        ];
    }
}
