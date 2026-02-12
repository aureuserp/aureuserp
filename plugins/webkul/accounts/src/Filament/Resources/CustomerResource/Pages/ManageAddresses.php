<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
