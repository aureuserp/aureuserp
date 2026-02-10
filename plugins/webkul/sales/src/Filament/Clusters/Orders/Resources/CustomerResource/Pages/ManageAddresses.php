<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageAddresses extends BaseManageAddresses
{
    use HasRecordNavigationTabs;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected static string $resource = CustomerResource::class;
}
