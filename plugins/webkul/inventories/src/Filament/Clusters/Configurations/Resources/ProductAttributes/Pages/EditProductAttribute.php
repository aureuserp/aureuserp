<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributes\ProductAttributeResource;
use Webkul\Product\Filament\Resources\Attributes\Pages\EditAttribute;

class EditProductAttribute extends EditAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
