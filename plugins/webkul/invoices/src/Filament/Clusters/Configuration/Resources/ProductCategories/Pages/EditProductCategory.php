<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\EditCategory;

class EditProductCategory extends EditCategory
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
