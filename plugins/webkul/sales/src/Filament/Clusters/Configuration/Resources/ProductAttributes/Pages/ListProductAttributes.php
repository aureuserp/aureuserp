<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages\ListProductAttributes as BaseListProductAttributes;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\ProductAttributeResource;

class ListProductAttributes extends BaseListProductAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
