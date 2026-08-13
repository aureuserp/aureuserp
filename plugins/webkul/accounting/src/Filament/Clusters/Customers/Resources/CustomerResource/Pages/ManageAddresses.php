<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
