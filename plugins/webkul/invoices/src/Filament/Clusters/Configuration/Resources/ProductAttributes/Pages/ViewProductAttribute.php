<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributes\ProductAttributeResource;
use Webkul\Product\Filament\Resources\Attributes\Pages\ViewAttribute;

class ViewProductAttribute extends ViewAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
