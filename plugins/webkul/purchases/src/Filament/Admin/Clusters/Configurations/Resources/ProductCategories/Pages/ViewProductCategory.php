<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Product\Filament\Resources\Categories\Pages\ViewCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;

class ViewProductCategory extends ViewCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
