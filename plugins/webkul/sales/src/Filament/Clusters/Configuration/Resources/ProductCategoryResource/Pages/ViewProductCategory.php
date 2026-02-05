<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ViewProductCategory as BaseViewProductCategory;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewProductCategory extends BaseViewProductCategory
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductCategoryResource::class;
}
