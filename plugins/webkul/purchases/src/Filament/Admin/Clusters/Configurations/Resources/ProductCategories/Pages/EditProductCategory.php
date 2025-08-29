<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Product\Filament\Resources\Categories\Pages\EditCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;

class EditProductCategory extends EditCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
