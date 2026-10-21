<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ListAttributes as BaseListAttributes;

class ListAttributes extends BaseListAttributes
{
    protected static string $resource = AttributeResource::class;
}
