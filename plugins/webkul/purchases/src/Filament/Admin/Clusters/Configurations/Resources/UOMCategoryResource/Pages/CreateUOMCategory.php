<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\UOMCategoryResource;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\CreateUOMCategory as BaseCreateUOMCategory;

class CreateUOMCategory extends BaseCreateUOMCategory
{
    protected static string $resource = UOMCategoryResource::class;
}
