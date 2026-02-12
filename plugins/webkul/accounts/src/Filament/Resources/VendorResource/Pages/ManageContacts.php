<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = VendorResource::class;
}
