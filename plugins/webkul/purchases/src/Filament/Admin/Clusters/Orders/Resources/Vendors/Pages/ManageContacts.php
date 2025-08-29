<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ManageContacts as BaseManageContacts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = VendorResource::class;
}
