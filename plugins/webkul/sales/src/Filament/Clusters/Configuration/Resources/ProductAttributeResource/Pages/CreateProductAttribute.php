<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\CreateProductAttribute as BaseCreateProductAttribute;

class CreateProductAttribute extends BaseCreateProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
