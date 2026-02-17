<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = VendorResource::class;
}
