<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\CreatePartner;

class CreateVendor extends CreatePartner
{
    protected static string $resource = VendorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['supplier_rank'] = 1;

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('accounts::filament/resources/vendor/pages/create-vendor.title');
    }
}
