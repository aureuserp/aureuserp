<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
