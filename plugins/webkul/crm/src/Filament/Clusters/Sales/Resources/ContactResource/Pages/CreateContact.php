<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\CreatePartner as BaseCreatePartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class CreateContact extends BaseCreatePartner
{
    protected static string $resource = ContactResource::class;
}
