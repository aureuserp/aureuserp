<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Filament\Resources\VendorResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ViewPartner;

class ViewVendor extends ViewPartner
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('accounts::filament/resources/vendor/pages/view-vendor.title');
    }
}
