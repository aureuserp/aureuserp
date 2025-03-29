<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ManageProducts as BaseManageProducts;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
