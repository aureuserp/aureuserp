<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributes\Pages;

use Webkul\Product\Filament\Resources\Attributes\Pages\CreateAttribute;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributes\ProductAttributeResource;

class CreateProductAttribute extends CreateAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
