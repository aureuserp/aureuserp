<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\EditPartner as BaseEditPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class EditContact extends BaseEditPartner
{
    protected static string $resource = ContactResource::class;
}
