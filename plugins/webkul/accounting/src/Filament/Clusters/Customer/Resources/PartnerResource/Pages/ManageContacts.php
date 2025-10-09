<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = PartnerResource::class;
}
