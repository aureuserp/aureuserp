<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Filament\Actions\ViewAction;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\EditPartner as BaseEditPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class EditCustomer extends BaseEditPartner
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof ViewAction,
        ));
    }
}
