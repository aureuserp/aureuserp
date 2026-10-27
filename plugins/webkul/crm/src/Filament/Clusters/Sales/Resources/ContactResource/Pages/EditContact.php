<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Filament\Actions\ViewAction;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\EditPartner as BaseEditPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class EditContact extends BaseEditPartner
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof ViewAction,
        ));
    }
}
