<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\EditAttribute as BaseEditAttribute;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditAttribute extends BaseEditAttribute
{
    use HasRecordNavigationTabs;

    protected static string $resource = AttributeResource::class;
}
