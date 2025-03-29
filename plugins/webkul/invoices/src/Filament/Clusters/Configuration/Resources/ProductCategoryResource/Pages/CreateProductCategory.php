<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\CreateCategory;

class CreateProductCategory extends CreateCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
