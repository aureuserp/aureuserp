<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\ViewProductAttribute as BaseViewProductAttribute;

class ViewProductAttribute extends BaseViewProductAttribute
{
    protected static string $resource = ProductAttributeResource::class;
}
