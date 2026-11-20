<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\CreatePartner as BaseCreatePartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class CreateCustomer extends BaseCreatePartner
{
    protected static string $resource = CustomerResource::class;
}
