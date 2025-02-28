<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\EditVendor as BaseEditPartner;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource;

class EditPartner extends BaseEditPartner
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
