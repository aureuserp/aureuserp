<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;

    public  static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
