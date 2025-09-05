<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\ManageProducts as BaseManageProducts;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
