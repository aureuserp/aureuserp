<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = ContactResource::class;
}
