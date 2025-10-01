<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ManageProducts as BaseManageProducts;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageProducts extends BaseManageProducts
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductCategoryResource::class;
}
