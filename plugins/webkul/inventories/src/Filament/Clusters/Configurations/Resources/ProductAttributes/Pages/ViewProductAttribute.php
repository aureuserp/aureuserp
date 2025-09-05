<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\ProductAttributeResource;
use Webkul\Product\Filament\Resources\Attributes\Pages\ViewAttribute;

class ViewProductAttribute extends ViewAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
