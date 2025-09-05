<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages\CreateProductAttribute as BaseCreateProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\ProductAttributeResource;

class CreateProductAttribute extends BaseCreateProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
