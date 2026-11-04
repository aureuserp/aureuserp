<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
