<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\CreateVendor as BaseCreatePartner;

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
