<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;
use Webkul\Product\Filament\Resources\Categories\Pages\ListCategories;

class ListProductCategories extends ListCategories
{
    protected static string $resource = ProductCategoryResource::class;
}
