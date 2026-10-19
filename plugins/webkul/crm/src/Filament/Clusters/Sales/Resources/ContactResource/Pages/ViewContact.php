<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ViewPartner as BaseViewPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ViewContact extends BaseViewPartner
{
    protected static string $resource = ContactResource::class;
}
