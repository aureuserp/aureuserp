<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = PartnerResource::class;
}
