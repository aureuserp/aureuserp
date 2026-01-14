<?php

namespace Webkul\Account\Filament\Resources\ProductCategoryResource\Pages;

use Webkul\Account\Filament\Resources\ProductCategoryResource;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\EditCategory;

class EditProductCategory extends EditCategory
{
    protected static string $resource = ProductCategoryResource::class;
}
