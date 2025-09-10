<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages\EditProductAttribute as BaseEditProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributes\ProductAttributeResource;

class EditProductAttribute extends BaseEditProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
