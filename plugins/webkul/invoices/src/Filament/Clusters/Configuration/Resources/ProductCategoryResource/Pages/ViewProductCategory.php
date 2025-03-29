<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ViewCategory;

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
