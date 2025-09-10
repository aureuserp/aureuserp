<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\Pages;

use Webkul\Product\Filament\Resources\Categories\Pages\ManageProducts as BaseManageProducts;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategories\ProductCategoryResource;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
