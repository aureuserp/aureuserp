<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributes\Pages;

use Webkul\Product\Filament\Resources\Attributes\Pages\ListAttributes;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributes\ProductAttributeResource;

class ListProductAttributes extends ListAttributes
{
    protected static string $resource = ProductAttributeResource::class;
}
