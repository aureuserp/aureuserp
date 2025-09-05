<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\VendorResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\EditPartner as BaseEditVendor;

class EditVendor extends BaseEditVendor
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/edit-vendor.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/edit-vendor.sub-navigation.edit-vendor');
    }
}
