<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = ContactResource::class;
}
