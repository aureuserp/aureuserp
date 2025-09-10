<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\ProductAttributeResource;
use Webkul\Product\Filament\Resources\Attributes\Pages\ListAttributes;

class ListProductAttributes extends ListAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
