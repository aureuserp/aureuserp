<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Filament\Actions\EditAction;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ViewPartner as BaseViewPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ViewContact extends BaseViewPartner
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof EditAction,
        ));
    }
}
