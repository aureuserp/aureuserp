<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\ViewCategory;

class ViewProductCategory extends ViewCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
