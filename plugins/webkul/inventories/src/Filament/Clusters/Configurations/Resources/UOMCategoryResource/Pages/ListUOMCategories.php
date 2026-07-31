<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\UOMCategoryResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ListUOMCategories as BaseListUOMCategories;

class ListUOMCategories extends BaseListUOMCategories
{
    protected static string $resource = UOMCategoryResource::class;
}
