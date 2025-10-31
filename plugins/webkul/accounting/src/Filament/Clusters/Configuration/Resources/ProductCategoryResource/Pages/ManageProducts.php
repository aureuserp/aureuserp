<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ManageProducts as BaseManageProducts;

class ManageProducts extends BaseManageProducts
{
    protected static string $resource = ProductCategoryResource::class;
}
