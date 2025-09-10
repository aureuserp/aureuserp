<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\ProductAttributeResource;
use Webkul\Product\Filament\Resources\Attributes\Pages\ListAttributes;

class ListProductAttributes extends ListAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
