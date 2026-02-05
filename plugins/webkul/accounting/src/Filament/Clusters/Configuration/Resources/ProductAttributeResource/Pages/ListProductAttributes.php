<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ListAttributes;

class ListProductAttributes extends ListAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
