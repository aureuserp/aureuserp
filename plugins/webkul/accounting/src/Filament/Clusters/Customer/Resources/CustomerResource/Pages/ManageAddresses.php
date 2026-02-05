<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageAddresses as BaseManageAddresses;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
