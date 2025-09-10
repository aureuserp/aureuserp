<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\CreateCategory;

class CreateProductCategory extends CreateCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
