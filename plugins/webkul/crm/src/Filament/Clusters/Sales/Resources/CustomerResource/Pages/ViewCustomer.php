<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Filament\Actions\EditAction;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ViewPartner as BaseViewPartner;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class ViewCustomer extends BaseViewPartner
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getHeaderActions(),
            fn ($action): bool => ! $action instanceof EditAction,
        ));
    }
}
