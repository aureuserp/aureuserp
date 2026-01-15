<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\CreateVendor as BaseCreateCustomer;

class CreateCustomer extends BaseCreateCustomer
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_rank'] = 1;

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
