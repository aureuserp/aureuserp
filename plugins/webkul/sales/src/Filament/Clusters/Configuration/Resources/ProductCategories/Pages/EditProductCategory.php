<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategories\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategories\Pages\EditProductCategory as BaseEditProductCategory;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategories\ProductCategoryResource;

class EditProductCategory extends BaseEditProductCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
