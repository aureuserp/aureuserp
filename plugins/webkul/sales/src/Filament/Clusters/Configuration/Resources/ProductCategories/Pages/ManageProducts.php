<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages\ManageProducts as BaseManageProducts;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
