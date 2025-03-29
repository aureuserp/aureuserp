<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ListProductAttributes as BaseListProductAttributes;

class ListProductAttributes extends BaseListProductAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
