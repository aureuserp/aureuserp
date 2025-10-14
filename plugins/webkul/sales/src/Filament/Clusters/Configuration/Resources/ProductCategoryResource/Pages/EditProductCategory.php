<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\EditProductCategory as BaseEditProductCategory;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditProductCategory extends BaseEditProductCategory
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductCategoryResource::class;
}
