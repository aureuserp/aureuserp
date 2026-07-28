<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ListUOMCategories as BaseListUOMCategories;

class ListUOMCategories extends BaseListUOMCategories
{
    protected static string $resource = UOMCategoryResource::class;
}
