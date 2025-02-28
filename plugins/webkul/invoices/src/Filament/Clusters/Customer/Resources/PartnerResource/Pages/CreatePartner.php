<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\CreateVendor as BaseCreatePartner;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource;

class CreatePartner extends BaseCreatePartner
{
    protected static string $resource = PartnerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sub_type'] = 'customer';

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
