<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\UOMCategoryResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ListUOMCategories as BaseListUOMCategories;

class ListUOMCategories extends BaseListUOMCategories
{
    protected static string $resource = UOMCategoryResource::class;
}
