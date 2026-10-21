<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ViewAttribute as BaseViewAttribute;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewAttribute extends BaseViewAttribute
{
    use HasRecordNavigationTabs;

    protected static string $resource = AttributeResource::class;
}
