<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ListAttributes;

class ListProductAttributes extends ListAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
