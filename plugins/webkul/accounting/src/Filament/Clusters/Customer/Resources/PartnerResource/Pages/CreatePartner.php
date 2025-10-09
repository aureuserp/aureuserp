<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\CreateVendor as BaseCreatePartner;

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
