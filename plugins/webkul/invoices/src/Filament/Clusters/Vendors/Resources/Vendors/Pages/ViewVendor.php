<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\VendorResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\ViewPartner as BaseViewPartner;

class ViewVendor extends BaseViewPartner
{
    protected static string $resource = VendorResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/view-vendor.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/vendor/pages/view-vendor.sub-navigation.view-vendor');
    }
}
