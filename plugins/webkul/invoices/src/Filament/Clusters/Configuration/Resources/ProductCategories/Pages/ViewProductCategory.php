<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\ViewCategory;

class ViewProductCategory extends ViewCategory
{
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
