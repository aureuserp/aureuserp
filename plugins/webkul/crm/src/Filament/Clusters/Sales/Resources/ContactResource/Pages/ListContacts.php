<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ListContacts extends BaseListPartners
{
    protected static string $resource = ContactResource::class;
}
