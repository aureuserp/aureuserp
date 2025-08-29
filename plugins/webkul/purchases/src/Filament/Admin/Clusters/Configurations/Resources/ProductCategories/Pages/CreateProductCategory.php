<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Product\Filament\Resources\Categories\Pages\CreateCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;

class CreateProductCategory extends CreateCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
