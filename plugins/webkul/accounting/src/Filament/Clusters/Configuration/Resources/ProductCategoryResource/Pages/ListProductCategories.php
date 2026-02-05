<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ListCategories;

class ListProductCategories extends ListCategories
{
    protected static string $resource = ProductCategoryResource::class;
}
