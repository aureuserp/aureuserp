<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
