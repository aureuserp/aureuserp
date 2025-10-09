<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\PartnerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\EditVendor as BaseEditPartner;

class EditPartner extends BaseEditPartner
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
