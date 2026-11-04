<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
