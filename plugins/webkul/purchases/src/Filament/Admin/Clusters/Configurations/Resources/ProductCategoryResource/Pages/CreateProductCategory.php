<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource;

class CreateProductCategory extends CreateCategory
{
    protected static string $resource = ProductCategoryResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
