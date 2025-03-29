<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\EditProductAttribute as BaseEditProductAttribute;

class EditProductAttribute extends BaseEditProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
