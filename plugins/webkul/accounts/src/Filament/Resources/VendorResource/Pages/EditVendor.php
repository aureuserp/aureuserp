<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\EditPartner;

class EditVendor extends EditPartner
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('accounts::filament/resources/vendor/pages/edit-vendor.title');
    }
}
