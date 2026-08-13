<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = VendorResource::class;
}
