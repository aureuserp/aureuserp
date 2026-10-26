<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Concerns\HasCrmTableViews;

class ListContacts extends BaseListPartners
{
    use HasCrmTableViews;

    protected static string $resource = ContactResource::class;

    public function getPresetTableViews(): array
    {
        return parent::getPresetTableViews();
    }
}
