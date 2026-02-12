<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\CreatePartner;

class CreateCustomer extends CreatePartner
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_rank'] = 1;

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('accounts::filament/resources/customer/pages/create-customer.title');
    }
}
